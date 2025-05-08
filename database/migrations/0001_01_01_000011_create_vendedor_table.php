<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('vendedores', function (Blueprint $table) {
      $table->string('uuid')->unique();
      $table->string('nome');
      $table->string('email');
      $table->timestamps();
    });

    Schema::create('vendedores_aliases', function (Blueprint $table) {
      $table->id();
      $table->uuid('vendedor_uuid');
      $table->string('alias')->unique();
      $table->timestamps();
    });

    Schema::table('leads', function (Blueprint $table) {
      $table->dropColumn('vendedor'); // Remove o nome antigo
      $table->uuid('vendedor_uuid')->after('telefone'); // Novo campo com UUID
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('vendedores');
    Schema::dropIfExists('vendedores_aliases');
  }
};
