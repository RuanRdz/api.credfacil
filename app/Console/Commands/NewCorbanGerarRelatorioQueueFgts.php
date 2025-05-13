<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewCorbanRelatorioApiService;

class NewCorbanGerarRelatorioQueueFgts extends Command {
  protected $signature = 'newcorban:gerarqueuefgts {inicio?} {fim?}';
  protected $description = 'Gera um CSV das consultas da fila de FGTS';

  public function handle(NewCorbanRelatorioApiService $api) {
    try {
      $retorno = $api->gerarRelatorio('queue_fgts', $this->argument('inicio'), $this->argument('fim'));
      $this->info("{$this->signature} -> [$retorno]");
    } catch (\Exception $e) {
      $this->error(json_encode([
        'erro' => $e->getMessage()
        , 'exception' => $e
      ]));
    }
  }
}
