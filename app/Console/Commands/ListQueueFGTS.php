<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Controllers\NewCorbanConsultaController;
use App\Http\Controllers\NewcorbanQueueFgtsController;

class ListQueueFGTS extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'newcorban:list-queue-fgts {--dias=1}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Retorna a lista de FGTS do newcorban';

  /**
   * Execute the console command.
   */
  public function handle() {
    $dias = (int) $this->option('dias') ?? 1;
    $dataInicio = now()->subDays($dias)->toDateString();
    $dataFim = now()->toDateString();
    
    $aPayload = [
      'data' => [
        'startDate' => $dataInicio,
        'endDate' => $dataFim
      ]
    ];

    $oNewCorbanConsultaController = new NewCorbanConsultaController();
    $aRetorno = $oNewCorbanConsultaController->send('listQueueFGTS', $aPayload);

    $oNewcorbanQueueFgtsController = new NewcorbanQueueFgtsController();

    foreach ($aRetorno as $aConsulta) {
      $oNewcorbanQueueFgtsController->storeAndSend($aConsulta);
    }
  }
}
