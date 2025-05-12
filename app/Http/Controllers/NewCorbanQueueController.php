<?php

namespace App\Http\Controllers;

use App\Models\NewCorbanQueue;
use Illuminate\Support\Carbon;
use Exception;

class NewCorbanQueueController extends Controller {
  public function store($id, $csvDataBase64) {
    try {
      NewCorbanQueue::deleted(['consulta_id' => $id]);

      $csvData = base64_decode($csvDataBase64);
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
        #consulta ainda não foi concluída
        if(empty($data['Data Conclusão'])) {
          continue;
        }
        $data = array_combine($headers, str_getcsv(implode(';', $row), ';'));
        $dataConsulta = Carbon::createFromFormat('d/m/Y H:i:s', $data['Data Conclusão']);
        $dataConsultaFormatada = $dataConsulta->format('Y-m-d');
        $cpfFormatado = Util::formatCpf($data['CPF']);

        $payload = [
          'consulta_id' => $id,
          'cpf' => $cpfFormatado,
          'data' => $dataConsultaFormatada,
          'status' => empty($data['Status']) ? null : $data['Status'],
          'telefone' => empty($data['Telefone']) ? null : $data['Telefone'],
          'saldo' => Util::parseDecimal($data['Saldo']),
          'valor_liberado' => Util::parseDecimal($data['Valor Liberado']),
          'data_consulta' => $dataConsulta
        ];
        
        NewCorbanQueue::updateOrCreate(
          ['cpf' => $cpfFormatado, 'data' => $dataConsultaFormatada], 
          $payload
        );
      }
      return true;
    } catch (Exception $e) {
      throw($e);
    }
  }
}