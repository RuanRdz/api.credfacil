<?php

namespace App\Http\Controllers;

use App\Models\NewCorbanQueue;
use Illuminate\Support\Carbon;
use Exception;

class NewCorbanQueueController extends Controller {
  public function store($id, $csvDataBase64) {
    try {
      $csvData = base64_decode($csvDataBase64);
      #$csvData = preg_replace('/^\xEF\xBB\xBF/', '', $csvData);
      $lines = explode("\n", $csvData);

      // Garante que a primeira linha é uma string
      $headerLine = array_shift($lines);
      if (!is_string($headerLine)) {
        throw new \Exception("Cabeçalho CSV inválido.");
      }

      $headers = array_map('trim', str_getcsv($headerLine, ';'));
      $rows = array_map(fn($line) => str_getcsv($line, ';'), $lines);

      foreach ($rows as $row) {
        if (count($row) < count($headers)) {
          continue;
        }
        $data = array_combine($headers, str_getcsv(implode(';', $row), ';'));
        #consulta ainda não foi concluída
        if(empty($data['Data Conclusão'])) {
          continue;
        }
        $dataConsulta = Carbon::createFromFormat('d/m/Y H:i:s', $data['Data Conclusão']);
        $dataConsultaFormatada = $dataConsulta->format('Y-m-d');
        $cpfFormatado = Util::formatCpf($data['CPF']);

        $conditions = [
          'cpf'  => $cpfFormatado,
          'data' => $dataConsultaFormatada,
        ];

        $novoValor = Util::parseDecimal($data['Valor Liberado']);

        $payload = [
          'consulta_id'     => $id,
          'cpf'             => $cpfFormatado,
          'data'            => $dataConsultaFormatada,
          'status'          => empty($data['Status']) ? null : $data['Status'],
          'telefone'        => empty($data['Telefone']) ? null : $data['Telefone'],
          'saldo'           => Util::parseDecimal($data['Saldo']),
          'valor_liberado'  => $novoValor,
          'data_consulta'   => $dataConsulta,
        ];

        $registroExistente = NewCorbanQueue::where($conditions)->first();
        $deveAtualizar = false;
        if (!$registroExistente) {
          $deveAtualizar = true; // novo registro
        } elseif ($registroExistente->status !== 'Consultado') {
          $deveAtualizar = true; // sempre atualiza se não for "Consultado"
        } else {
          $novoValor = Util::parseDecimal($data['Valor Liberado']);
          $valorAtual = $registroExistente->valor_liberado ?? 0;
          if ($novoValor > $valorAtual) {
            $deveAtualizar = true; // atualiza apenas se valor maior
          }
        }

        if ($deveAtualizar) {
          NewCorbanQueue::updateOrCreate($conditions, $payload);
        }
      }
      return true;
    } catch (Exception $e) {
      var_dump($e);
      throw($e);
    }
  }
}