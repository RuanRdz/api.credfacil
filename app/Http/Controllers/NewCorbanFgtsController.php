<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VendedorController;
use App\Models\NewCorbanFgts;
use Illuminate\Support\Carbon;
use Exception;

class NewCorbanFgtsController extends Controller {
  public function store($id, $csvDataBase64) {
    try {
      NewcorbanFgts::deleted(['consulta_id' => $id]);

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
          continue; // ignora linhas incompletas
        }

        $data = array_combine($headers, str_getcsv(implode(';', $row), ';'));
        

        $oVendedor = new VendedorController();
        $vendedorUuid = $oVendedor->store($data['Usuario']);
        $payload = [
          'consulta_id'        => $id,
          'vendedor_uuid'      => $vendedorUuid,
          'cpf'                => Util::formatCpf($data['CPF']),
          'saldo'              => $this->parseDecimal($data['Saldo']),
          'valor_liberado'     => $this->parseDecimal($data['Valor Liberado']),
          'tabela_simulada'    => empty($data['Tabela Simulada']) ? null : $data['Tabela Simulada'],
          'data_consulta'      => \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['Data da Consulta']),
          'ultima_tentativa'   => \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['Última Tentativa']),
          'flag'               => empty($data['Flag']) ? null : $data['Flag'],
          'proposta_gerada'    => empty($data['Proposta Gerada']) ? null : $data['Proposta Gerada'],
          'proposta_cancelada' => isset($data['Proposta Cancelada']) && $data['Proposta Cancelada'] !== '' ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['Proposta Cancelada']) : null,
          'proposta_paga'      => isset($data['Proposta Paga']) && $data['Proposta Paga'] !== '' ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $data['Proposta Paga']) : null,
        ];
        NewcorbanFgts::create($payload);
      }
      return true;
    } catch (Exception $e) {
      throw($e);
      return false;
    }
  }

  function parseDecimal($value) {
    $numeric = preg_replace('/[^\d,]/', '', $value);
    return $numeric === '' ? 0 : floatval(str_replace(',', '.', $numeric));
  }
}