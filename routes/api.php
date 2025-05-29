<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PropostaController;
use App\Http\Controllers\NewcorbanQueueFgtsController;

Route::middleware('check.priora')->group(function () {
  Route::post('/leads', [LeadController::class, 'store']);
  Route::post('/consulta', [NewcorbanQueueFgtsController::class, 'storeFromGuru']);
  Route::get('/propostas', [PropostaController::class, 'index']);
  Route::get('/propostas/{id}', [PropostaController::class, 'show']);
});

Route::middleware('check.newcorban.token')->group(function () {
  Route::post('/retornoConsulta', [NewcorbanQueueFgtsController::class, 'storeFromNewcorban']);
});