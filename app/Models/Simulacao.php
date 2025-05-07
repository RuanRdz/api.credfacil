<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Simulacao extends Model {
  protected $table = 'simulacoes';
  protected $fillable = [
    'telefone'
    , 'vendedor'
    , 'data'
    , 'tipo'
  ];

  public $timestamps = true;

  protected static function boot() {
    parent::boot();
    static::creating(function ($model) {
      $model->uuid = (string) Str::uuid();
    });
  }
}