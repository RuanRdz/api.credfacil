<?php
namespace App\Http\Controllers;

use App\Models\Status;

class StatusController extends Controller {
  public function store($sDescricao) {
    $alias = strtolower(trim($sDescricao));
    $aStatus = Status::whereRaw('LOWER(descricao) = ?', [$alias])->first();
    if (!$aStatus) {
      $aStatus = [
        'descricao' => $sDescricao
      ];
      $aStatus = Status::create($aStatus);
    }
    return $aStatus['id'];
  }
}