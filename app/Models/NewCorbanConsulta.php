<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NewcorbanConsulta extends Model {
  protected $table = 'newcorban_consultas';

  protected $fillable = [
    'api_id',
    'tipo',
    'api_created_at',
    'api_finished_at',
  ];

  protected $casts = [
    'api_created_at' => 'datetime',
    'api_finished_at' => 'datetime',
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}
