<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VendedorController;
use App\Models\NewCorbanFgts;
use Illuminate\Support\Carbon;
use Exception;

class NewCorbanFgtsController extends Controller {
  public function store($id, $csvData) {
    try {
      // Remove o BOM do início da string (caso exista)
      $csvData = preg_replace('/^\xEF\xBB\xBF/', '', $csvData);
      $lines = explode("\n", $csvData);

      // Lógica de definição do vendedor
      $userApi = env('USER_API');

      foreach ($lines as $sRow) {
        $row = explode(';', $sRow);
        if(!isset($row[0]) || empty($row[0]) || $row[0] == 'Instituição') {
          continue; // ignora linhas incompletas
        }

        $data = str_getcsv(implode(';', $row), ';');

        $dataConsulta = Carbon::createFromFormat('d/m/Y H:i:s', $data[7]);
        $dataConsultaFormatada = $dataConsulta->format('Y-m-d');
        $cpfFormatado = Util::formatCpf($data[2]);

        $oVendedor = new VendedorController();
        $vendedorUuid = $oVendedor->store($data[13]);

        $conditions = [
          'cpf'  => (string) $cpfFormatado,
          'data' => (string) $dataConsultaFormatada,
        ];

        $novoValor = $this->parseDecimal($data[4]);

        $payload = [
          'consulta_id'        => $id,
          'vendedor_uuid'      => $vendedorUuid,
          'cpf'                => $cpfFormatado,
          'data'               => $dataConsultaFormatada,
          'saldo'              => $this->parseDecimal($data[3]),
          'valor_liberado'     => $novoValor,
          'tabela_simulada'    => empty($data[5]) ? null : $data[5],
          'data_consulta'      => Carbon::createFromFormat('d/m/Y H:i:s', $data[6]),
          'ultima_tentativa'   => $dataConsulta,
          'flag'               => empty($data[8]) ? null : $data[8],
          'proposta_gerada'    => Util::parseDataBr(empty($data[10]) ? null : $data[10]),
          'proposta_cancelada' => isset($data[11]) && $data[11] !== '' ? Carbon::createFromFormat('d/m/Y H:i:s', $data[11]) : null,
          'proposta_paga'      => isset($data[12]) && $data[12] !== '' ? Carbon::createFromFormat('d/m/Y H:i:s', $data[12]) : null,
          'instituicao'        => empty($data[0]) ? null : $data[0],
          'robo'               => empty($data[1]) ? 0 : 1
        ];

        $registroExistente = NewCorbanFgts::where($conditions)->first();

        if (!$registroExistente) {
          // Se não existir, apenas cria
          NewCorbanFgts::create($payload);
          continue;
        }

        if ($vendedorUuid !== $userApi || $registroExistente->vendedor_uuid === $userApi) {
          $payload['vendedor_uuid'] = $vendedorUuid;
        } else {
          unset($payload['vendedor_uuid']);
        }

        // Verifica se precisa atualizar
        $valorAtual = floatval($registroExistente->valor_liberado ?? 0);
        $deveAtualizar = $registroExistente->flag !== null || $novoValor > $valorAtual;

        if ($deveAtualizar) {
          // Apenas atualiza se houver diferença nos dados
          $mudancas = array_diff_assoc($payload, $registroExistente->only(array_keys($payload)));
          if (!empty($mudancas)) {
            $registroExistente->update($payload);
          }
        } elseif (isset($payload['vendedor_uuid'])) {
          // Atualiza somente o vendedor se necessário
          if ($registroExistente->vendedor_uuid !== $payload['vendedor_uuid']) {
            $registroExistente->update([
              'vendedor_uuid' => $payload['vendedor_uuid'],
            ]);
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