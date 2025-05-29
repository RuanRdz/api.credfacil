<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NewcorbanQueueFgts extends Model
{
  use HasFactory;

  protected $table = 'newcorban_queue_fgts';

  protected $fillable = [
    'cliente_id',
    'tabela',
    'status_id',
    'vendedor_uuid',
    'instituicao',
    'saldo',
    'valor_liberado',
    'data_inclusao',
    'data_ult_consulta',
    'data_concluido',
    'api',
    'error_message',
    'vendedor',
    'proposta_id',
    'data_pagamento',
    'data_cancelado',
  ];

  protected $casts = [
    'data_inclusao' => 'datetime',
    'data_ult_consulta' => 'datetime',
    'data_concluido' => 'datetime',
    'data_pagamento' => 'datetime',
    'data_cancelado' => 'datetime',
    'api' => 'boolean',
    'saldo' => 'float',
    'valor_liberado' => 'float',
  ];
}
