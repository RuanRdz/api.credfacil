<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteSemSaldoController;

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
});

require __DIR__.'/auth.php';
