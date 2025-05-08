<?php

namespace App\Models;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class VendedorAlias extends Model {
  protected $table = 'vendedores_aliases';
  protected $fillable = [
    'vendedor_uuid'
    , 'alias'
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}
