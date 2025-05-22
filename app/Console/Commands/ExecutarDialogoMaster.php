<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;

class ExecutarDialogoMaster extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:executar-dialogo-master';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   */
  public function handle() {
    $aPhonesId = explode(',', env('CHATGURU_PHONE_ID'));
    $this->info(env('CHATGURU_PHONE_ID'));
    $this->info(json_encode($aPhonesId));
    foreach($aPhonesId as $sPhoneId) {
      $this->info('Executando para o phoneid:' . $sPhoneId);
      try {
        $aPayload = [
          'chat_number' => '554799638161',
          'key'         => env('CHATGURU_API_KEY'),
          'account_id'  => env('CHATGURU_ACCOUNT_ID'),
          'phone_id'    => $sPhoneId,
          'action'      => 'dialog_execute',
          'dialog_id'   => env('CHATGURU_MASTER_DIALOG_ID'),
        ];
        $response = Http::asForm()->post(env('CHATGURU_API_URL'), $aPayload);

        if ($response->successful()) {
          $this->info('Diálogo executado com sucesso:');
          $this->line(json_encode($response->json(), JSON_PRETTY_PRINT));
        } else {
          $this->error('Erro ao executar diálogo:');
          $this->line(json_encode($aPayload));
          $this->line($response->body());
        }
      } catch (Exception $oExcept) {
        $this->info('Erro ao executar diálogo:');
        $this->line(json_encode($aPayload));
        $this->line($response->body());
      }
    }

    return 0;
  }
}
