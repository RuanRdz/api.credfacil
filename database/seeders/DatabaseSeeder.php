<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;
use App\Http\Controllers\Util;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    $arquivo = storage_path('app/clientes.csv'); // coloque o CSV aqui

    if (!File::exists($arquivo)) {
      $this->command->error("Arquivo não encontrado: $arquivo");
      return;
    }

    $linhas = file($arquivo, FILE_IGNORE_NEW_LINES);
    $cabecalho = str_getcsv(array_shift($linhas), ';');

    foreach ($linhas as $linha) {
      $dados = array_combine($cabecalho, str_getcsv($linha, ';'));


      $entrada = null;
      if (!empty($dados['ENTRADA'])) {
        try {
          $entrada = Carbon::createFromFormat('d/m/Y H:i:s', $dados['ENTRADA'])->format('Y-m-d H:i:s');
        } catch (\Exception $e) {}
      }

      $ultimaInteracao = null;
      if (!empty($dados['ÚLTIMA INTERAÇÃO'])) {
        try {
          $ultimaInteracao = Carbon::createFromFormat('d/m/Y H:i:s', $dados['ÚLTIMA INTERAÇÃO'])->format('Y-m-d H:i:s');
        } catch (\Exception $e) {}
      }

      $telefone = $dados['TELEFONE'] ?? null;
      $nome = $dados['NOME'] ?? null;
      $cpf = $dados['CPF'] ?? null;
      $mes = $dados['MES'] ?? null;
      $uf = $dados['UF'] ?? null;
      $vendedor = $dados['VENDEDOR'] ?? null;
      $tipo = $dados['Tipo'] ?? null;
      $antecipou = $dados['Atencipou'] === 'Sim' ? 'Sim' : 'Não';
      $acompanhamento = $dados['Acompanhamento'] === 'Sim' ? 'Sim' : 'Não';
      $linkChat = $dados['LINK CHAT'] ?? null;
      $trafego = $dados['TRAFEGO'] ?? 'Normal';

      $sCpfFormatado = Util::formatCpf($cpf);
      $key = ['telefone' => $telefone];
      if (!empty($sCpfFormatado)) {
        $key = ['cpf' => $sCpfFormatado];
      }

      Cliente::updateOrCreate(
        $key,
        [
          'telefone' => $telefone,
          'nome' => $nome,
          'cpf' => $sCpfFormatado,
          'mes' => $mes,
          'uf' => $uf,
          'vendedor' => $vendedor,
          'tipo' => $tipo,
          'antecipou' => $antecipou,
          'acompanhamento' => $acompanhamento,
          'entrada' => $entrada,
          'ultima_interacao' => $ultimaInteracao,
          'link_chat' => $linkChat,
          'trafego' => $trafego,
        ]
      );
    }

    $this->command->info('Clientes importados com sucesso!');
  }
}
