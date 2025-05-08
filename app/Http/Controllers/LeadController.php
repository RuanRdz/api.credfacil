<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Http\Controllers\ClienteController;
use Illuminate\Support\Str;

class LeadController extends Controller {
  public function store(Request $request) {
    $oCliente = new ClienteController();
    $oCliente->store($request);

    $telefone = $request->input('email');
    $vendedor = $request->input('responsavel_nome');
    $dataHoje = now()->toDateString();

    // Verifica se já existe
    $leadExistente = Lead::where('telefone', $telefone)
                          ->whereDate('data', $dataHoje)
                          ->first();

    if ($leadExistente) {
      return response()->json([
        'success' => true,
        'lead' => $leadExistente->uuid
      ], 409); // 409 Conflict
    }

    $uid = Lead::create([
      'telefone' => $telefone,
      'vendedor' => $vendedor,
      'data' => $dataHoje,
      'uuid' => Str::uuid()
    ]);

    return response()->json([
      'success' => true,
      'lead' => $uid->uuid
    ], 201);
  }
}
