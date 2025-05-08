<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Controllers
use App\Http\Controllers\LeadController;
use App\Http\Controllers\SimulacaoMasterController;
use App\Http\Controllers\SimulacaoMiniController;
use App\Http\Controllers\SemSaldoController;
use App\Http\Controllers\ContratoController;

Route::middleware('check.priora')->group(function () {
  Route::post('/leads', [LeadController::class, 'store']);
});