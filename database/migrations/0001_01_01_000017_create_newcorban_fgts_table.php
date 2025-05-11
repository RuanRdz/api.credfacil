<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('newcorban_fgts', function (Blueprint $table) {
      $table->id();
      $table->string('consulta_id');
      $table->string('cpf')->nullable();
      $table->decimal('saldo', 15, 2)->nullable();
      $table->decimal('valor_liberado', 15, 2)->nullable();
      $table->string('tabela_simulada')->nullable();
      $table->timestamp('data_consulta')->nullable();
      $table->timestamp('ultima_tentativa')->nullable();
      $table->string('flag')->nullable();
      $table->string('proposta_gerada')->nullable();
      $table->timestamp('proposta_cancelada')->nullable();
      $table->timestamp('proposta_paga')->nullable();
      $table->string('vendedor_uuid');
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('newcorban_fgts');
  }
};
