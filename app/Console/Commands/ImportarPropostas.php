<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\PropostaController;
use Illuminate\Support\Facades\Log;

class ImportarPropostas extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'importar:propostas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa propostas da API NewCorban';

    /**
     * Execute the console command.
     */
    public function handle() {
      $response = Http::withHeaders([
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
        'Accept' => 'application/json, text/plain, */*',
        'Accept-Language' => 'pt-BR,pt;q=0.9',
        'Referer' => env('API_URL'),
        'Origin' => env('API_URL'),
      ])->post(env('API_URL') . '/api/propostas/', [
        'auth' => [
          'username' => env('API_USERNAME'),
          'password' => env('API_PASSWORD'),
          'empresa'  => env('API_EMPRESA'),
        ],
        'requestType' => 'getPropostas',
        'filters' => [
          'status' => [],
          'data' => [
            'tipo' => 'cadastro',
            'startDate' => now()->toDateString(),
            'endDate' => now()->toDateString(),
          ],
        ],
      ]);
      
      if ($response->failed()) {
        $status = $response->status();
        $body = $response->body();
        
        $this->error("Erro na chamada da API. Status: $status");
        
        Log::error('Erro ao buscar propostas da API', [
          'status' => $status,
          'body' => $body
        ]);
        
        return;
      }

      $dados = $response->json();

      $oProposta = new PropostaController();

      foreach ($dados as $idProposta => $proposta) {
        $ddd = $proposta['cliente']['telefones'][array_key_first($proposta['cliente']['telefones'])]['ddd'] ?? null;
        $telefone = $proposta['cliente']['telefones'][array_key_first($proposta['cliente']['telefones'])]['numero'] ?? null;
        $aProposta = [
          'proposta_id' => $idProposta
          , 'cpf' => $proposta['cliente']['cliente_cpf']
          , 'data_cadastro' => $proposta['datas']['cadastro'] ?? null
          , 'data_pagamento' => $proposta['datas']['pagamento'] ?? null
          , 'valor_liberado' => $proposta['proposta']['valor_liberado'] ?? 0
          , 'valor_referencia' => $proposta['proposta']['valor_referencia'] ?? 0
          , 'valor_financiado' => $proposta['proposta']['valor_financiado'] ?? 0
          , 'vendedor_nome' => $proposta['vendedor_nome']
          , 'telefone' => $ddd.$telefone
          , 'status' => $proposta['api']['status_api']
        ];
        $oProposta->store($aProposta);
      }
    }
}
