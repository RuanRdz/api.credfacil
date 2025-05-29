<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Carbon;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\NewCobarnApiController;
use App\Http\Controllers\StatusController;
use App\Models\NewcorbanQueueFgts;

class NewcorbanQueueFgtsController extends Controller
{
  const errorIgnore = [
    'Não foi possível incluir o CPF na fila, verifique se já não existe.'
  ];

  const errorConsultaNovamente = [
    'valor da emissão da operação (issue_amount) é superior ao valor máximo permitido.'
  ];

  public function storeAndSend($aConsulta) {
    $sLoginBanco = str_replace(',', ';', env('LOGIN_BANCO'));

    try {
      if(!in_array($aConsulta['error_message'], self::errorConsultaNovamente)) {
        return false;
      }
      $oStatusController = new StatusController();
      $statusId = $oStatusController->store($aConsulta['status_descricao']);

      NewcorbanQueueFgts::updateOrCreate(['id' => $aConsulta['id']], [
          'cpf' => $aConsulta['cpf']
        , 'telefone' => $aConsulta['telefone']
        , 'tabela' => $aConsulta['tabela']
        , 'status_id' => $statusId
        , 'instituicao' => $aConsulta['instituicao']
        , 'instituicao' => $aConsulta['instituicao']
        , 'data_inclusao' => Carbon::parse($aConsulta['data_inclusao'])
        , 'data_ult_consulta' => Carbon::parse($aConsulta['data_ult_consulta'])
        , 'data_concluido' => Carbon::parse($aConsulta['data_concluido'])
      ]);

      $aPayload = [
        'content' => [
            'cpf' => Util::onlyNumber($aConsulta['cpf'])
          , 'instituicao' => 'v8'
          , 'login_banco' => $sLoginBanco
          , 'tabela' => env('TABELA_BANCO')
          , 'telefone' => $aConsulta['telefone']
          , 'webhook_url' => env('WEBHOOK_URL')
        ] 
      ];

      $oNewCobarnApiController = new NewCobarnApiController();
      $aConsulta = $oNewCobarnApiController->send('insertQueueFGTS', $aPayload);

      if($aConsulta['error']) {
        if(in_array($aConsulta['mensagem'], self::errorIgnore)) {
          return response()->json([
            'success' => true
            , 'message' => $aConsulta['mensagem']
          ], 201);
        }
        throw new Exception(sprintf(
          '%s. [%s]'
          , $aConsulta['mensagem']
          , json_encode($aPayload)
        ));
      }

      NewcorbanQueueFgts::create(['id' => $aConsulta['id']], [
          'cpf' => $aConsulta['cpf']
        , 'telefone' => $aConsulta['telefone']
        , 'tabela' => env('TABELA_BANCO')
        , 'status_id' => 1
        , 'instituicao' => 'v8'
      ]);

      return response()->json([
        'success' => true
        , 'message' => 'CPF incluído na fila de consulta'
        , 'consulta_id' => $aConsulta['id']
      ], 201);
    } catch (Exception $e) {
      Log::error('Erro ao refazer a consulta', [
        'exception' => $e->getMessage()
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao refazer a consulta.'
      ], 500);
    }
  }

  public function storeFromGuru(Request $request) {
    $vendedor = null;
    $email = null;
    $sLoginBanco = str_replace(',', ';', env('LOGIN_BANCO'));

    try {
      $vendedor = $request->input('responsavel_nome');
      $uidVendedor = null;

      //garante o cadastro do cliente
      $oCliente = new ClienteController();
      $aCliente = $oCliente->store($request);
      
      //garante o cadastro do vendedor
      if(!empty($vendedor)) {
        $email = $request->input('responsavel_email');
        $oVendedor = new VendedorController();
        $uidVendedor = $oVendedor->store($vendedor, $email);
      }

      $aConsulta = NewcorbanQueueFgts::create([
          'cliente_id' => $aCliente['id']
        , 'tabela' => env('TABELA_BANCO')
        , 'status_id' => 1 // aguardando envio
        , 'vendedor_uuid' => $uidVendedor
        , 'instituicao' => 'v8'
      ]);      
      
      $aPayload = [
        'content' => [
            'cpf' => Util::onlyNumber($aCliente['cpf'])
          , 'instituicao' => 'v8'
          , 'login_banco' => $sLoginBanco
          , 'tabela' => env('TABELA_BANCO')
          , 'telefone' => $aCliente['telefone']
          , 'webhook_url' => sprintf(
            '%s?token=%sid=%s'
            , env('WEBHOOK_URL')
            , env('NEWCORBAN_TOKEN')
            , $aConsulta['id']
          )
        ] 
      ];

      $oNewCobarnApiController = new NewCobarnApiController();
      $aConsultaRetorno = $oNewCobarnApiController->send('insertQueueFGTS', $aPayload);

      $oConsulta = NewcorbanQueueFgts::where(['id' => $aConsulta['id']]);

      if($aConsultaRetorno['error']) {
        $oConsulta->update([
          'status_id' => 2
          , 'error_message' => $aConsultaRetorno['mensagem']
        ]);
        if(in_array($aConsultaRetorno['mensagem'], self::errorIgnore)) {
          return response()->json([
            'success' => true
            , 'message' => $aConsultaRetorno['mensagem']
          ], 201);
        }
        throw new Exception(sprintf(
          '%s. [%s]'
          , $aConsultaRetorno['mensagem']
          , json_encode($aPayload)
        ));
      } else {
        $oConsulta->update([
          'consulta_id' => $aConsultaRetorno['id']
        ]);
      }

      return response()->json([
        'success' => true
        , 'message' => 'CPF incluído na fila de consulta'
        , 'consulta_id' => $aConsulta['id']
      ], 201);
    } catch (Exception $e) {
      Log::error('Erro ao refazer a consulta', [
        'exception' => $e->getMessage()
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao refazer a consulta.'
      ], 500);
    }
  }

  public function storeFromNewcorban(Request $request) {
    try {
      $consultaId = $request->query('id');
      $oConsulta = NewcorbanQueueFgts::where(['id' => $consultaId]);
      $oConsulta->update([
        'saldo' => $request->input('saldo')
        , 'valor_liberado' => $request->input('valor_liberado')
        , 'data_inclusao' => Carbon::parse($request->input('data_inclusao'))
        , 'data_ult_consulta' => Carbon::parse($request->input('data_ult_consulta'))
        , 'data_concluido' => Carbon::parse($request->input('data_concluido'))
        , 'error_message' => $request->input('error_message')
        , 'vendedor' => $request->input('vendedor')
        , 'proposta_id' => $request->input('proposta_id')
        , 'data_pagamento' => $request->input('data_pagamento')
        , 'data_cancelado' => $request->input('data_cancelado')
      ]);

      return response()->json([
        'success' => true
        , 'consulta_id' => $consultaId
      ], 201);
    } catch (Exception $e) {
      Log::error('Erro ao armazenar retorno da consulta.', [
        'exception' => $e->getMessage()
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Erro ao armazenar retorno da consulta.'
      ], 500);
    }
  }
}
