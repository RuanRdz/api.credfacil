<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NewCorbanQueue extends Model {
  protected $table = 'newcorban_queue';

  protected $fillable = [
    'consulta_id',
    'cpf',
    'data',
    'status',
    'telefone',
    'saldo',
    'valor_liberado',
    'data_consulta',
  ];

  protected $casts = [
    'data_consulta' => 'datetime'
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}