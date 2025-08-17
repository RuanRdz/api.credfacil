<?php

namespace App\Http\Controllers\V8;

use Illuminate\Http\Request;
use App\Models\V8\Balance;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ClienteController;
use App\Services\V8ApiService;
use Illuminate\Support\Facades\Log;
use Exception;

class BalanceController extends Controller {
  public function storeAndPost(Request $request) {
    $telefone = null;
    $vendedor = null;
    $email = null;
    try {
      $oCliente = new ClienteController();
      $aCliente = $oCliente->store($request);

      Balance::create(['cliente_id' => $aCliente['id']]);

      $oV8ApiService = new V8ApiService();
      $oV8ApiService->balance($aCliente['cpf']);
    } catch (Exception $e) {
      Log::error('Erro ao armazenar consulta', [
        'exception' => $e->getMessage(),
        'data' => [
          'telefone' => $telefone
          , 'vendedor' => $vendedor
          , 'email' => $email
        ]
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao processar a consulta.'
      ], 500);
    }
  }
}