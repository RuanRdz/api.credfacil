<?php

namespace App\Http\Controllers;

use App\Models\NewCorbanConsulta;

class NewCorbanConsultaController extends Controller {
  public function store($data) {
    NewCorbanConsulta::updateOrCreate(['api_id' => $data['id']], [
      'api_id' => $data['id']
      , 'tipo' => $data['tipo']
      , 'api_created_at' => $data['created_at']
      , 'api_finished_at' => $data['finished_at']
    ]);
  }

  public function get($apiId)   {
    $consulta = NewCorbanConsulta::where('api_id', $apiId)->first();
    if ($consulta) {
      return true;
    }
    return false;
  }

  public function delete($apiId)   {
    NewCorbanConsulta::where('api_id', $apiId)->delete() > 0;
  }
}