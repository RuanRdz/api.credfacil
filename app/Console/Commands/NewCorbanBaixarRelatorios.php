<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewCorbanRelatorioApiService;
use App\Http\Controllers\NewcorbanConsultaController;
use App\Http\Controllers\NewcorbanFgtsController;
use App\Http\Controllers\NewcorbanQueueController;

class NewCorbanBaixarRelatorios extends Command {
  protected $signature = 'newcorban:baixar';
  protected $description = 'Baixa relatórios prontos a cada hora';

  public function handle(NewCorbanRelatorioApiService $api) {
    try {
      $relatorios = $api->buscarRelatorios();
      $oNewcorbanConsultaController = new NewcorbanConsultaController();
      foreach ($relatorios as $rel) {
        if(!$oNewcorbanConsultaController->get($rel['id'])) {
          $fileContent = $api->baixarRelatorio($rel['id']);
          if(!empty($fileContent)) {
            switch($rel['tipo']) {
              case 'saldos_fgts':
                $this->info(sprintf('salvando [%s, %s]', $rel['id'], $rel['tipo']));
                $oNewcorbanFgtsController = new NewcorbanFgtsController();
                if($oNewcorbanFgtsController->store($rel['id'], $fileContent)) {
                  $oNewcorbanConsultaController->store($rel);
                }
                break;
              case 'queue_fgts':
                $this->info(sprintf('salvando [%s, %s]', $rel['id'], $rel['tipo']));
                $oNewcorbanQueueController = new NewcorbanQueueController();
                if($oNewcorbanQueueController->store($rel['id'], $fileContent)) {
                  $oNewcorbanConsultaController->store($rel);
                }
                break;
            }
          }
          
          sleep(3);
        }
      }
    } catch (\Exception $e) {
      $this->error("Erro: " . $e->getMessage());
    }
  }
}
