<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteSemSaldoController;
use App\Http\Controllers\LogViewerController;

Route::get('/', function () {
  return redirect()->route('clientes.sem.saldo');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
  return redirect()->route('clientes.sem.saldo');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  Route::get('/clientes-sem-saldo', [ClienteSemSaldoController::class, 'index'])->name('clientes.sem.saldo');
  Route::get('/clientes-sem-saldo/exportar', [ClienteSemSaldoController::class, 'exportarCsv'])->name('clientes.sem.saldo.exportar');
  Route::get('/logs', [LogViewerController::class, 'index'])->name('logs.index');
  Route::post('/logs/clear', [LogViewerController::class, 'clear'])->name('logs.clear');
});

require __DIR__.'/auth.php';
