<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckPrioraToken;
use App\Http\Middleware\CheckNewCorbanToken;
use Illuminate\Console\Scheduling\Schedule;

$oApp = Application::configure(basePath: dirname(__DIR__))
  ->withRouting(
    api: __DIR__.'/../routes/api.php',
    web: __DIR__.'/../routes/web.php',
  )
  ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
      'check.priora' => CheckPrioraToken::class,
      'check.newcorban.token' => CheckNewCorbanToken::class,
    ]);
  })
  ->withExceptions(function (Exceptions $exceptions) {
    //
  })
  ->withSchedule(function (Schedule $schedule) {
    // Executa a cada 5 minutos (ex: busca dos últimos 5-10 minutos)
    $schedule->command('importar:propostas')->everyFiveMinutes()->withoutOverlapping();

    // Executa diariamente às 2h da manhã para buscar os últimos 30 dias
    $schedule->command('importar:propostas --dias=30')->dailyAt('02:00');

    // A cada 30 minutos (ex: 00:00, 00:30, 01:00, ...)
    $schedule->command('newcorban:gerarsaldofgts')->cron('0,30 * * * *')->withoutOverlapping();

    // 6 minutos depois de gerarsaldofgts
    $schedule->command('newcorban:baixar')->cron('6,36 * * * *')->withoutOverlapping();

    // 11 minutos depois de gerarsaldofgts
    $schedule->command('guru:dialogo-master')->cron('11,41 * * * *')->withoutOverlapping();
  })
  ->withCommands([
    \App\Console\Commands\ImportarPropostas::class,
    \App\Console\Commands\NewCorbanGerarRelatorioSaldoFgts::class,
    \App\Console\Commands\NewCorbanGerarRelatorioQueueFgts::class,
    \App\Console\Commands\NewCorbanBaixarRelatorios::class,
    \App\Console\Commands\ExecutarDialogoMaster::class,
  ])
  ->create();

return $oApp;