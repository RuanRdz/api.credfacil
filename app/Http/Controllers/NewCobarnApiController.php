<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewCobarnApiController extends Controller
{
  public function send($sRequestType, $aPayload)
  {
    $payload = array_merge([
      'auth' => [
        'username' => env('API_USERNAME'),
        'password' => env('API_PASSWORD'),
        'empresa'  => env('API_EMPRESA'),
      ],
      'requestType' => $sRequestType,
    ], $aPayload);

    $response = Http::withHeaders([
      'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
      'Accept' => 'application/json, text/plain, */*',
      'Accept-Language' => 'pt-BR,pt;q=0.9',
      'Referer' => env('API_URL'),
      'Origin' => env('API_URL'),
    ])->post(env('API_URL') . '/api/propostas/', $payload);

    if ($response->failed()) {
      Log::error('Erro ao integrar API', [
        'status' => $response->status(),
        'body' => $response->body(),
        'param' => $payload,
      ]);

      return false;
    }

    return $response->json();
  }
}
