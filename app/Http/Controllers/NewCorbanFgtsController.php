<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VendedorController;
use App\Models\NewCorbanFgts;
use Illuminate\Support\Carbon;
use Exception;

class NewCorbanFgtsController extends Controller {
  public function store($id, $csvDataBase64) {
    try {
      $csvData = base64_decode($csvDataBase64);
      // Remove o BOM do início da string (caso exista)
      $csvData = preg_replace('/^\xEF\xBB\xBF/', '', $csvData);
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

        $dataConsulta = Carbon::createFromFormat('d/m/Y H:i:s', $data['Última Tentativa']);
        $dataConsultaFormatada = $dataConsulta->format('Y-m-d');
        $cpfFormatado = Util::formatCpf($data['CPF']);

        $oVendedor = new VendedorController();
        $vendedorUuid = $oVendedor->store($data['Usuario']);

        $conditions = [
          'cpf'  => (string) $cpfFormatado,
          'data' => (string) $dataConsultaFormatada,
        ];

        $novoValor = $this->parseDecimal($data['Valor Liberado']);
        if($data['Mensagem'] == 'valor da emissão da operação (issue_amount) é superior ao valor máximo permitido.') {
          $data['Flag'] = 'MASTER';
        }

        $payload = [
          'consulta_id'        => $id,
          'vendedor_uuid'      => $vendedorUuid,
          'cpf'                => $cpfFormatado,
          'data'               => $dataConsultaFormatada,
          'saldo'              => $this->parseDecimal($data['Saldo']),
          'valor_liberado'     => $novoValor,
          'tabela_simulada'    => empty($data['Tabela Simulada']) ? null : $data['Tabela Simulada'],
          'data_consulta'      => Carbon::createFromFormat('d/m/Y H:i:s', $data['Data da Consulta']),
          'ultima_tentativa'   => $dataConsulta,
          'flag'               => empty($data['Flag']) ? null : $data['Flag'],
          'proposta_gerada'    => Util::parseDataBr(empty($data['Proposta Gerada']) ? null : $data['Proposta Gerada']),
          'proposta_cancelada' => isset($data['Proposta Cancelada']) && $data['Proposta Cancelada'] !== '' ? Carbon::createFromFormat('d/m/Y H:i:s', $data['Proposta Cancelada']) : null,
          'proposta_paga'      => isset($data['Proposta Paga']) && $data['Proposta Paga'] !== '' ? Carbon::createFromFormat('d/m/Y H:i:s', $data['Proposta Paga']) : null,
          'instituicao'        => empty($data['Instituição']) ? null : $data['Instituição'],
          'robo'               => empty($data['Robô']) ? 0 : 1
        ];

        $registroExistente = NewCorbanFgts::where($conditions)->first();
        if (!$registroExistente) {
          NewCorbanFgts::create($payload);
        } else {
          if($registroExistente->vendedor_uuid == env('USER_API')) {
            $payload['vendedor_uuid'] = $vendedorUuid;
          } else {
            if($vendedorUuid != env('USER_API')) {
              $payload['vendedor_uuid'] = $vendedorUuid;
            } else {
              unset($payload['vendedor_uuid']);
            }
          }
          $novoValor = $this->parseDecimal($data['Valor Liberado']);
          $valorAtual = $registroExistente->valor_liberado ?? 0;
          if ($registroExistente->flag !== null || $novoValor > $valorAtual) {
            NewCorbanFgts::updateOrCreate(
              $conditions, 
              $payload
            );
          } else {
            if(isset($payload['vendedor_uuid'])) {
              NewCorbanFgts::updateOrCreate(
                $conditions, 
                ['vendedor_uuid' => $payload['vendedor_uuid']]
              );
            }
          }
        }
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