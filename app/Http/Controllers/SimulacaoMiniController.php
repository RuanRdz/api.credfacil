<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Simulacao;
use App\Http\Controllers\ClienteController;

class SimulacaoMiniController extends Controller {

  public function store(Request $request) {
    $oCliente = new ClienteController();
    $oCliente->store($request);
    
    $telefone = $request->input('email');
    $vendedor = $request->input('responsavel_nome');

    $uid = Simulacao::create([
      'telefone' => $telefone
      , 'vendedor' => $vendedor
      , 'tipo' => 'MINI'
      , 'data' => now()
    ]);

    // Retorna resposta JSON
    return response()->json([
      'success' => true,
      'simulacao' => $uid['uuid']
    ], 201);
  }
}
