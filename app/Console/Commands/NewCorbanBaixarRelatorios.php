<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewCorbanRelatorioApiService;
use App\Http\Controllers\NewCorbanConsultaController;
use App\Http\Controllers\NewCorbanFgtsController;
use App\Http\Controllers\NewCorbanQueueController;

class NewCorbanBaixarRelatorios extends Command {
  protected $signature = 'newcorban:baixar';
  protected $description = 'Baixa relatórios prontos a cada hora';

  public function handle(NewCorbanRelatorioApiService $api) {
    try {
      $relatorios = $api->buscarRelatorios();
      usort($relatorios, function ($a, $b) {
        return intval($a['id']) <=> intval($b['id']);
      });
      $oNewCorbanConsultaController = new NewCorbanConsultaController();
      foreach ($relatorios as $rel) {
        if(!$oNewCorbanConsultaController->get($rel['id'])) {
          $fileContent = $api->baixarRelatorio($rel['id']);
          if(!empty($fileContent)) {
            switch($rel['tipo']) {
              case 'saldos_fgts':
                $this->info(sprintf('salvando [%s, %s]', $rel['id'], $rel['tipo']));
                $oNewCorbanFgtsController = new NewCorbanFgtsController();
                if($oNewCorbanFgtsController->store($rel['id'], $fileContent)) {
                  $oNewCorbanConsultaController->store($rel);
                }
                break;
              case 'queue_fgts':
                $this->info(sprintf('salvando [%s, %s]', $rel['id'], $rel['tipo']));
                $oNewCorbanQueueController = new NewCorbanQueueController();
                if($oNewCorbanQueueController->store($rel['id'], $fileContent)) {
                  $oNewCorbanConsultaController->store($rel);
                }
                break;
            }
          }
          
          sleep(3);
        }
      }
    } catch (\Exception $e) {
      $this->error("Erro: " . $e->getMessage());
      throw($e);
    }
  }
}
