<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;

class SlackController extends Controller {
  /**
   * Enviar mensagem para o usuário do Slack
   *
   * @param string $sUserId   ID do usuário ou canal no Slack
   * @param string|array $sMessage  Mensagem (texto simples ou array de blocks)
   * @return array  Resposta da API do Slack
   */
  public function sendSlackMessage(string $sUserId, $sMessage): array {
    $payload = is_array($sMessage)
      ? ['channel' => $sUserId, 'blocks' => $sMessage]
      : ['channel' => $sUserId, 'text' => $sMessage]; // suporta texto simples também

    $response = Http::withToken(env('SLACK_TOKEN'))
      ->acceptJson()
      ->post(env('SLACK_URL'), $payload);

    return $response->json();
  }
}