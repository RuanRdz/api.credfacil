<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\NewCorbanFgts;
use Exception;

class ExecutarDialogoMaster extends Command {
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'guru:dialogo-master';

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
    $registros = DB::table('newcorban_fgts AS f')
      ->join('clientes AS c', 'f.cpf', '=', 'c.cpf')
      ->select('f.id', 'f.cpf', 'f.flag', 'c.telefone')
      ->where('f.flag', 'MASTER')
      ->whereNotNull('c.telefone')
      ->get();

    foreach ($registros as $r) {
      try {
        $aPayload = [
          'chat_number' => $r->telefone,
          'key'         => env('CHATGURU_API_KEY'),
          'account_id'  => env('CHATGURU_ACCOUNT_ID'),
          'phone_id'    => env('CHATGURU_PHONE_ID'),
          'action'      => 'dialog_execute',
          'dialog_id'   => env('CHATGURU_MASTER_DIALOG_ID'),
        ];
        $response = Http::asForm()->post(env('CHATGURU_API_URL'), $aPayload);
        $responseBody = isset($response) ? $response->body() : 'sem resposta';

        $json = $response->json();
        $foiExecutado = $response->successful()
          && ($json['result'] ?? '') === 'success'
          && ($json['dialog_execution_return'] ?? '') === 'Diálogo Executado';

        if ($foiExecutado) {
          NewCorbanFgts::where('id', $r->id)->update(['flag' => null]);
        } else {
          Log::info(sprintf(
            'Erro ao executar diálogo: payload[%s]. response[%s]',
            json_encode($aPayload),
            $responseBody
          ));
        }
      } catch (Exception $oExcept) {
        Log::info(sprintf(
          'Exception ao executar diálogo: [%s]. payload[%s]. response[%s]'
          , $oExcept->getMessage()
          , json_encode($aPayload)
          , $responseBody
        ));
      }
    }
    return 0;
  }
}
