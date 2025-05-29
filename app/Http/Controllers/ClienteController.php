<?php
namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller {
  public function store(Request $request) {
    $dados = $request->all();

    $telefone = $dados['email'] ?? null;
    $nome = $dados['nome'] ?? null;
    $cpf = $dados['campos_personalizados']['CPF'] ?? null;
    $tags = $dados['tags'] ?? [];

    $mes = null;
    $antecipou = 'Não';
    $acompanhamento = 'Não';
    $tipo = null;
    $uf = null;
    $trafego = 'Normal';

    $mesesDoAno = [
      'Janeiro'
      , 'Fevereiro'
      , 'Março'
      , 'Abril'
      , 'Maio'
      , 'Junho'
      , 'Julho'
      , 'Agosto'
      , 'Setembro'
      , 'Outubro'
      , 'Novembro'
      , 'Dezembro'
    ];

    $estadosBrasil = [
      'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF'
      , 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA'
      , 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS'
      , 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
    ];

    foreach ($tags as $tag) {
      if (in_array($tag, $mesesDoAno)) $mes = $tag;
      if ($tag === 'Antecipou') $antecipou = 'Sim';
      if ($tag === 'Acompanhamento') $acompanhamento = 'Sim';
      if ($tag === 'Mini' && !$tipo) $tipo = 'Mini';
      if ($tag === 'Master') $tipo = 'Master';
      if (in_array($tag, ['[A4]', '[A7]'])) $trafego = 'Pago';
      if (in_array($tag, $estadosBrasil)) $uf = $tag;
    }

    $vendedor = $dados['responsavel_nome'] ?? null;
    $linkChat = $dados['link_chat'] ?? null;
    $entrada = $dados['chat_created'] ?? null;
    $ultimaInteracao = $dados['datetime_post'] ?? null;
    $sCpfFormatado = Util::formatCpf($cpf);

    $payLoad = [
      'telefone' => $telefone
      , 'nome' => $nome
      , 'cpf' => $sCpfFormatado
      , 'mes' => $mes
      , 'uf' => $uf
      , 'vendedor' => $vendedor
      , 'tipo' => $tipo
      , 'antecipou' => $antecipou
      , 'acompanhamento' => $acompanhamento
      , 'entrada' => $entrada
      , 'ultima_interacao' => $ultimaInteracao
      , 'link_chat' => $linkChat
      , 'trafego' => $trafego
    ];
    
    // Tenta localizar pelo CPF
    $oCliente = Cliente::where('cpf', $sCpfFormatado)->first();
    // Se não encontrar pelo CPF, tenta pelo telefone
    if (!$oCliente) {
      if (empty($payLoad['cpf'])) {
        unset($payLoad['cpf']);
      }
      $oCliente = Cliente::where('telefone', $telefone)->first();
    }

    if ($oCliente) {
      $oCliente->update($payLoad);
      $aCliente = $oCliente->refresh()->toArray();
    } else {
      $aCliente = Cliente::create($payLoad)->toArray();
    }

    return $aCliente;
  }

  public function storeFromApi($aCliente) {
    $aCliente['cpf'] = Util::formatCpf($aCliente['cpf']);
    #se nao mandou o telefone, nao atualiza, para evitar sobrepor dados
    if(empty($aCliente['telefone'])) {
      unset($aCliente['telefone']);
    }
    Cliente::updateOrCreate(
      ['cpf' => $aCliente['cpf']],
      $aCliente
    );
  }
}