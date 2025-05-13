<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewCorbanRelatorioApiService;

class NewCorbanGerarRelatorioSaldoFgts extends Command {
  protected $signature = 'newcorban:gerarsaldofgts {inicio?} {fim?}';
  protected $description = 'Gera um relatório CSV das simulações realizadas';

  public function handle(NewCorbanRelatorioApiService $api) {
    try {
      $retorno = $api->gerarRelatorio('saldos_fgts', $this->argument('inicio'), $this->argument('fim'));
      $this->info("{$this->signature} -> [$retorno]");
    } catch (\Exception $e) {
      $this->error(json_encode([
        'erro' => $e->getMessage()
        , 'exception' => $e
      ]));
    }
  }
}
