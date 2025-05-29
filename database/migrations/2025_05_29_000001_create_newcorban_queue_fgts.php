<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up()
  {
    Schema::create('status', function (Blueprint $table) {
      $table->id();
      $table->string('descricao')->nullable();
    });

    DB::table('status')->insert([
      'id' => 1,
      'descricao' => 'Aguardando',
    ]);

    DB::table('status')->insert([
      'id' => 2,
      'descricao' => 'Erro',
    ]);

    Schema::create('newcorban_queue_fgts', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('consulta_id')->nullable();
      $table->unsignedBigInteger('cliente_id');
      $table->string('tabela');
      $table->unsignedBigInteger('status_id');
      $table->string('vendedor_uuid')->nullable();
      $table->string('instituicao')->nullable();
      $table->decimal('saldo', 10, 2)->nullable();
      $table->decimal('valor_liberado', 10, 2)->nullable();
      $table->timestamp('data_inclusao')->nullable();
      $table->timestamp('data_ult_consulta')->nullable();
      $table->timestamp('data_concluido')->nullable();
      $table->boolean('api')->default(false);
      $table->text('error_message')->nullable();
      $table->string('vendedor')->nullable();
      $table->unsignedBigInteger('proposta_id')->nullable();
      $table->timestamp('data_pagamento')->nullable();
      $table->timestamp('data_cancelado')->nullable();

      $table->timestamps();
    });
  }

  public function down() {
    Schema::dropIfExists('status');
    Schema::dropIfExists('newcorban_queue_fgts');
  }
};