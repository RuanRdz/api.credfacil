<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewCorbanRelatorioApiService;
use App\Http\Controllers\NewCorbanConsultaController;
use App\Http\Controllers\NewCorbanFgtsController;

class NewCorbanBaixarRelatorios extends Command {
  protected $signature = 'newcorban:baixar {id?}';
  protected $description = 'Baixa relatórios prontos a cada hora';

  public function handle(NewCorbanRelatorioApiService $api) {
    try {
      $id = $this->argument('id');
      $relatorios = $api->buscarRelatorios();
      usort($relatorios, function ($a, $b) {
        return intval($a['id']) <=> intval($b['id']);
      });
      $oNewCorbanConsultaController = new NewCorbanConsultaController();
      foreach ($relatorios as $rel) {
        if($rel['tipo'] != 'saldos_fgts') {
          continue;
        }
        if(!empty($id)) {
          if($id != $rel['id']) {
            continue;
          } else {
            #se está forçando um id especifico, vamos limpar ele para registrar novamente
            $oNewCorbanConsultaController->delete($id);
          }
        }
        if(!$oNewCorbanConsultaController->get($rel['id'])) {
          $fileContent = $api->baixarRelatorio($rel['id']);
          if(!empty($fileContent)) {
            $this->info(sprintf('salvando [%s, %s]', $rel['id'], $rel['tipo']));
            $oNewCorbanFgtsController = new NewCorbanFgtsController();
            if($oNewCorbanFgtsController->store($rel['id'], $fileContent)) {
              $oNewCorbanConsultaController->store($rel);
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
