<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Status extends Model
{
  use HasFactory;

  protected $table = 'status';

  public $timestamps = false;

  protected $fillable = [
    'descricao'
  ];
}
