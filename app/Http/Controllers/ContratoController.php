<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ClienteController;
use App\Models\Contrato;

class ContratoController extends Controller {

  public function store(Request $request) {
    $oCliente = new ClienteController();
    $oCliente->store($request);
    
    $telefone = $request->input('email');
    $vendedor = $request->input('responsavel_nome');
    
    $uid = Contrato::create([
      'telefone' => $telefone
      , 'vendedor' => $vendedor
      , 'data' => now()
    ]);

    // Retorna resposta JSON
    return response()->json([
      'success' => true,
      'contrato' => $uid['uuid']
    ], 201);
  }
}
