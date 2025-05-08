<?php

namespace App\Models;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model {
  protected $table = 'vendedores';
  protected $fillable = [
    'nome'
    , 'email'
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
