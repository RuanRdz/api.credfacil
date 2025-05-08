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
  })
  ->withCommands([
    \App\Console\Commands\ImportarPropostas::class,
  ])
  ->create();

return $oApp;