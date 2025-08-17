<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Util;

class V8ApiService {
  protected $url;
  protected $urlAuth;
  protected $grant_type;
  protected $scope;
  protected $client_id;
  protected $audience;
  protected $username;
  protected $password;
  protected $token;

  public function __construct() {
    $this->url = env('V8_URL');
    $this->urlAuth = env('V8_URL_AUTH');
    $this->grant_type = env('V8_GRANT');
    $this->scope = env('V8_SCOPE');
    $this->client_id = env('V8_CLIENTID');
    $this->audience = env('V8_AUDIENCE');
    $this->username = env('V8_USERNAME');
    $this->password = env('V8_PASSWORD');
  }

  public function balance($sCpf) {
    $this->login();
    $aPayload = [
      'documentNumber' => Util::onlyNumber($sCpf)
      , 'provider' => 'bms'
    ];
    $this->send($aPayload);
  }

  public function send(array $payload) {
    if (!$this->token) {
      Log::warning('Token ausente. Realizando login novamente.');
      $this->login();

      if (!$this->token) {
        Log::error('Falha ao obter token no método send.');
        return null;
      }
    }

    $response = Http::withToken($this->token)
      ->post($this->url . '/fgts/balance', $payload);

    if ($response->failed()) {
      Log::error('Erro ao enviar payload para V8', [
          'url' => $this->url . '/fgts/balance',
          'status' => $response->status(),
          'body' => $response->body(),
          'payload' => $payload
      ]);

      return null;
    }

    return $response->json();
  }
  
  public function login() {
    $aPayload = [
      'grant_type' => $this->grant_type
      , 'scope' => $this->scope
      , 'client_id' => $this->client_id
      , 'audience' => $this->audience
      , 'username' => $this->username
      , 'password' => $this->password
    ];
    $response = Http::post($this->urlAuth . '/oauth/token', $aPayload);
    
    $response = Http::withHeaders([
      'Accept' => 'application/json',
      'Authorization' => 'Bearer ' . $this->token,
    ])
    ->post($this->urlAuth . '/oauth/token', $aPayload);

    if ($response->failed()) {
      Log::error('Erro ao enviar payload para V8', [
        'url' => $this->url . '/oauth/token',
        'status' => $response->status(),
        'body' => $response->body(),
        'payload' => $aPayload
      ]);

      return null;
    }

    $aRetorno = $response->json();

    $this->token = $aRetorno['access_token'];
  }
}
