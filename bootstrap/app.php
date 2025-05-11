<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPrioraToken;
use Illuminate\Console\Scheduling\Schedule;

$oApp = Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    api: __DIR__.'/../routes/api.php'
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
      'check.priora' => CheckPrioraToken::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })
  ->withSchedule(function (Schedule $schedule) {
    $schedule->command('importar:propostas')->everyMinute();
    $schedule->command('newcorban:gerarsaldofgts')->everyMinute();
    $schedule->command('newcorban:gerarqueuefgts')->everyMinute();
    $schedule->command('newcorban:baixar')->everyMinute();
  })
  ->withCommands([
    \App\Console\Commands\ImportarPropostas::class,
    \App\Console\Commands\NewCorbanGerarRelatorioSaldoFgts::class,
    \App\Console\Commands\NewCorbanGerarRelatorioQueueFgts::class,
    \App\Console\Commands\NewCorbanBaixarRelatorios::class,
  ])
  ->create();

return $oApp;