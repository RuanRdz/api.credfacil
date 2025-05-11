<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('newcorban_queue', function (Blueprint $table) {
      $table->id();
      $table->string('consulta_id');
      $table->string('cpf')->nullable();
      $table->string('status')->nullable();
      $table->string('telefone')->nullable();
      $table->decimal('saldo', 15, 2)->nullable();
      $table->decimal('valor_liberado', 15, 2)->nullable();
      $table->timestamp('data_consulta')->nullable();
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('newcorban_queue');
  }
};
