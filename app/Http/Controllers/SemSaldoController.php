<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SemSaldo;
use App\Http\Controllers\ClienteController;

class SemSaldoController extends Controller {

  public function store(Request $request) {
    $oCliente = new ClienteController();
    $oCliente->store($request);
    
    $telefone = $request->input('email');
    $vendedor = $request->input('responsavel_nome');
    
    $uid = SemSaldo::create([
      'telefone' => $telefone
      , 'vendedor' => $vendedor
      , 'data' => now()
    ]);

    // Retorna resposta JSON
    return response()->json([
      'success' => true,
      'semSaldo' => $uid
    ], 201);
  }
}
