<?php

namespace App\Models;
use Illuminate\Support\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contrato extends Model {
  protected $fillable = [
    'telefone'
    , 'vendedor'
    , 'data'
  ];

  public $timestamps = true;

  protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
      $model->uuid = (string) Str::uuid();
    });
  }

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}