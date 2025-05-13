<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

class NewCorbanRelatorioApiService {
  protected $baseUrl;
  protected $usuario;
  protected $senha;
  protected $empresa;
  protected $ip;
  protected $origem;
  protected $referer;
  protected $userAgent = 'Mozilla/5.0';
  protected $token;

  public function __construct() {
    $this->baseUrl  = env('RELATORIO_API_BASE_URL');
    $this->usuario  = env('RELATORIO_API_USUARIO');
    $this->senha    = env('RELATORIO_API_SENHA');
    $this->empresa  = env('RELATORIO_API_EMPRESA');
    $this->ip       = env('RELATORIO_API_IP');
    $this->origem   = env('RELATORIO_API_ORIGEM');
    $this->referer  = env('RELATORIO_API_REFERER');
  }

  public function login() {
    $response = Http::asForm()->withHeaders([
      'Origin' => $this->origem,
      'Referer' => $this->referer,
      'User-Agent' => $this->userAgent
    ])->post("{$this->baseUrl}/api/v2/login", [
      'usuario' => $this->usuario,
      'senha' => $this->senha,
      'empresa' => $this->empresa,
      'ip' => $this->ip,
      'p' => 'web'
    ]);

    $response->throw();

    $this->token = $response->json('token');

    if (!$this->token) {
      throw new \Exception('Token não encontrado');
    }
  }

  protected function getAuthHeader(): array {
    if (!$this->token) {
      throw new \RuntimeException('Token não definido. Faça login primeiro.');
    }

    return [
      'Authorization' => "Bearer {$this->token}",
      'Origin' => $this->origem,
      'Referer' => $this->referer,
      'User-Agent' => $this->userAgent,
    ];
  }

  public function gerarRelatorio($tipo, $inicio = null, $fim = null) {
    $this->login();

    if(empty($inicio)) {
      $inicio = Carbon::now()->toDateString();
      $fim = Carbon::now()->toDateString();
    }

    $response = Http::asForm()
      ->withHeaders($this->getAuthHeader())
      ->post("{$this->baseUrl}/system/queue_fgts.php?action=export", [
        'filters[startDate]' => $inicio,
        'filters[endDate]' => $fim,
        'tipo' => $tipo,
      ]);

    $response->throw();

    return $response->json();
  }

  public function buscarRelatorios() {
    $this->login();

    $response = Http::withToken($this->token)
      ->withHeaders([
        'Accept' => 'application/json',
        'Origin' => $this->origem,
        'Referer' => $this->referer,
        'User-Agent' => $this->userAgent
      ])->get("{$this->baseUrl}/system/queue_fgts.php", [
        'action' => 'getExportHistory'
      ]);

    $response->throw();

    return $response->json();
  }

  public function baixarRelatorio($planilhaId) {
    $this->login();

    $response = Http::withToken($this->token)
      ->withHeaders([
        'Accept' => 'application/json',
        'Origin' => $this->origem,
        'Referer' => $this->referer,
        'User-Agent' => $this->userAgent
      ])->get("{$this->baseUrl}/system/queue_fgts.php", [
        'action' => 'getSheet',
        'id' => $planilhaId
      ]);

    $response->throw();

    if(!empty($response->json('fileName'))) {
      return $response->json('fileContent');
    }
  }
}
