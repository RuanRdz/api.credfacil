<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('propostas', function (Blueprint $table) {
      $table->id();
      $table->bigInteger('proposta_id')->unique();
      $table->string('cpf', 14);
      $table->dateTime('data_cadastro');
      $table->dateTime('data_pagamento')->nullable();
      $table->decimal('valor_liberado', 10, 2);
      $table->decimal('valor_referencia', 10, 2);
      $table->decimal('valor_financiado', 10, 2);
      $table->uuid('vendedor_uuid');
      $table->string('telefone', 20);
      $table->string('status')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('propostas');
  }
};