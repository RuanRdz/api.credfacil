<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Http\Controllers\ClienteController;

class LeadController extends Controller {

  public function store(Request $request) {
    $oCliente = new ClienteController();
    $oCliente->store($request);
    
    $telefone = $request->input('email');
    $vendedor = $request->input('responsavel_nome');

    $lead = Lead::create([
      'telefone' => $telefone
      , 'vendedor' => $vendedor
      , 'data' => now()
    ]);

    // Retorna resposta JSON
    return response()->json([
      'success' => true,
      'lead' => $lead
    ], 201);
  }
}
