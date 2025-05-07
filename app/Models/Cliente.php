<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model {
  protected $fillable = [
    'telefone'
    , 'nome'
    , 'cpf'
    , 'mes'
    , 'uf'
    , 'vendedor'
    , 'tipo'
    , 'antecipou'
    , 'acompanhamento'
    , 'entrada'
    , 'ultima_interacao'
    , 'link_chat'
    , 'trafego'
  ];

  protected $casts = [
    'entrada' => 'datetime',
    'ultima_interacao' => 'datetime',
  ];
}
