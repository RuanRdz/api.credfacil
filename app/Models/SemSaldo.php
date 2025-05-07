<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SemSaldo extends Model {
  protected $table = 'semsaldo';
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
}