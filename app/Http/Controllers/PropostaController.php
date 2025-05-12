<?php

namespace App\Http\Controllers;

use App\Models\Proposta;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ClienteController;

class PropostaController extends Controller {
  public function index() {
    return Proposta::latest()->paginate(20);
  }

  public function show($id) {
    $proposta = Proposta::findOrFail($id);
    return response()->json($proposta);
  }

  public function store($aProposta) {
    $oCliente = new ClienteController();
    $oCliente->storeFromApi($aProposta['cliente']);

    $oVendedor = new VendedorController();
    $aProposta['vendedor_uuid'] = $oVendedor->store($aProposta['vendedor_nome']);
    $aProposta['cpf'] = Util::formatCpf($aProposta['cpf']);
    
    $proposta = Proposta::updateOrCreate(
      ['proposta_id' => $aProposta['proposta_id']],
      $aProposta
    );
    return $proposta;
  }
}
