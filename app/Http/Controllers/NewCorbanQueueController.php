<?php

namespace App\Http\Controllers;

use App\Models\NewcorbanQueue;
use Illuminate\Support\Carbon;
use Exception;

class NewcorbanQueueController extends Controller {
  public function store($id, $csvDataBase64) {
    try {
      NewcorbanQueue::deleted(['consulta_id' => $id]);

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
        $data = array_combine($headers, str_getcsv(implode(';', $row), ';'));
        $payload = [
          'consulta_id' => $id,
          'cpf' => Util::formatCpf($data['CPF']),
          'status' => empty($data['Status']) ? null : $data['Status'],
          'telefone' => empty($data['Telefone']) ? null : $data['Telefone'],
          'saldo' => Util::parseDecimal($data['Saldo']),
          'valor_liberado' => Util::parseDecimal($data['Valor Liberado']),
          'data_consulta' => \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['Data Conclusão'])
        ];
        NewcorbanQueue::create($payload);
      }
      return true;
    } catch (Exception $e) {
      throw($e);
      return false;
    }
  }
}