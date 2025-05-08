<?php

namespace App\Models;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Proposta extends Model {
  protected $table = 'propostas';

  protected $fillable = [
    'proposta_id',
    'cpf',
    'data_cadastro',
    'data_pagamento',
    'valor_liberado',
    'valor_referencia',
    'valor_financiado',
    'vendedor_uuid',
    'telefone',
    'status',
  ];

  protected $casts = [
    'data_cadastro' => 'datetime',
    'data_pagamento' => 'datetime',
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}
