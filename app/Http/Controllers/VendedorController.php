<?php
namespace App\Http\Controllers;

use App\Models\Vendedor;
use App\Models\VendedorAlias;

class VendedorController extends Controller {
  public function store($nome, $email = '') {
    $alias = strtolower(trim($nome));
    $aVendedorUuid = VendedorAlias::whereRaw('LOWER(alias) = ?', [$alias])->first();
    if (!$aVendedorUuid) {
      $aVendedor = [
        'nome' => $nome
      ];
      if(!empty($email)) {
        $aVendedor['email'] = $email;
      }
      $aVendedor = Vendedor::create($aVendedor);

      $aVendedorUuid = VendedorAlias::create([
        'vendedor_uuid' => $aVendedor['uuid']
        , 'alias' => $alias
      ]);
    }
    return $aVendedorUuid['vendedor_uuid'];
  }
}