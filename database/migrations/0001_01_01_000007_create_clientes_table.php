<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('clientes', function (Blueprint $table) {
      $table->id();
      $table->string('telefone')->unique();
      $table->string('nome');
      $table->string('cpf')->nullable();
      $table->string('mes')->nullable();
      $table->string('uf')->nullable();
      $table->string('vendedor')->nullable();
      $table->string('tipo')->nullable();
      $table->string('antecipou')->default('Não');
      $table->string('acompanhamento')->default('Não');
      $table->timestamp('entrada')->nullable();
      $table->timestamp('ultima_interacao')->nullable();
      $table->text('link_chat')->nullable();
      $table->string('trafego')->default('Normal');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('clientes');
  }
};
