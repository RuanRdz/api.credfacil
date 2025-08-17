<?php

namespace App\Models\V8;
use Illuminate\Support\Carbon;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Balance extends Model {
  protected $table = 'v8_balance';

  protected $fillable = [
    'cliente_id'
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}