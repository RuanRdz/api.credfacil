<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class NewCorbanFgts extends Model {
  protected $table = 'newcorban_fgts';

  protected $fillable = [
    'consulta_id',
    'cpf',
    'data',
    'saldo',
    'valor_liberado',
    'tabela_simulada',
    'data_consulta',
    'ultima_tentativa',
    'flag',
    'proposta_gerada',
    'proposta_cancelada',
    'proposta_paga',
    'vendedor_uuid',
  ];

  protected $casts = [
    'data_consulta' => 'datetime',
    'ultima_tentativa' => 'datetime',
    'proposta_gerada' => 'datetime',
    'proposta_cancelada' => 'datetime',
    'proposta_paga' => 'datetime'
  ];

  public $timestamps = true;

  public function setCreatedAtAttribute($value) {
    $this->attributes['created_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }

  public function setUpdatedAtAttribute($value) {
    $this->attributes['updated_at'] = Carbon::parse($value)->setTimezone('America/Sao_Paulo');
  }
}