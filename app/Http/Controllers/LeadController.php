<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendedorController;
use Illuminate\Support\Facades\Log;
use Exception;

class LeadController extends Controller {
  public function store(Request $request) {
    $telefone = null;
    $vendedor = null;
    $email = null;
    try {
      $oCliente = new ClienteController();
      $oCliente->store($request);

      $telefone = $request->input('email');
      $vendedor = $request->input('responsavel_nome');
      $email = $request->input('responsavel_email');
      $dataHoje = now()->toDateString();

      $oVendedor = new VendedorController();
      $uidVendedor = $oVendedor->store($vendedor, $email);

      $uid = Lead::updateOrCreate(
        ['telefone' => $telefone, 'data' => $dataHoje],
        ['vendedor_uuid' => $uidVendedor]
      );

      return response()->json([
        'success' => true,
        'lead' => $uid->uuid
      ], 201);
    } catch (Exception $e) {
      Log::error('Erro ao armazenar lead', [
        'exception' => $e->getMessage(),
        'data' => [
          'telefone' => $telefone
          , 'vendedor' => $vendedor
          , 'email' => $email
        ]
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao processar o lead.'
      ], 500);
    }
  }
}