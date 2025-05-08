<?php

namespace App\Models;
use Illuminate\Support\Carbon;

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

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}
