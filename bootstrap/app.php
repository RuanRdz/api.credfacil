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
    // Executa a cada 5 minutos (ex: busca dos últimos 5-10 minutos)
    $schedule->command('importar:propostas')->everyFiveMinutes();
    // Executa diariamente às 2h da manhã para buscar os últimos 30 dias
    $schedule->command('importar:propostas --dias=30')->dailyAt('02:00');
    // Executa a cada hora em ponto (ex: 01:00, 02:00, etc.)
    $schedule->command('newcorban:gerarsaldofgts')->cron('0 * * * *');
    // Executa a cada hora no minuto 1 (ex: 01:01, 02:01, etc.)
    $schedule->command('newcorban:gerarqueuefgts')->cron('1 * * * *');
    // Executa a cada hora no minuto 2 (ex: 01:02, 02:02, etc.)
    $schedule->command('newcorban:baixar')->cron('2 * * * *');
  })
  ->withCommands([
    \App\Console\Commands\ImportarPropostas::class,
    \App\Console\Commands\NewCorbanGerarRelatorioSaldoFgts::class,
    \App\Console\Commands\NewCorbanGerarRelatorioQueueFgts::class,
    \App\Console\Commands\NewCorbanBaixarRelatorios::class,
  ])
  ->create();

return $oApp;