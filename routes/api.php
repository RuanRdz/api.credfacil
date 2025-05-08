<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PropostaController;

Route::middleware('check.priora')->group(function () {
  Route::post('/leads', [LeadController::class, 'store']);
  Route::get('/propostas', [PropostaController::class, 'index']);
  Route::get('/propostas/{id}', [PropostaController::class, 'show']);
});