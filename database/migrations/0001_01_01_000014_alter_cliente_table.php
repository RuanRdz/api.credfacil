<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::table('clientes', function (Blueprint $table) {
      $table->string('telefone')->nullable()->change();
      $table->dropUnique(['telefone']);
      $table->string('tipo')->nullable()->change();
      $table->string('antecipou')->nullable()->change();
      $table->string('acompanhamento')->nullable()->change();
      $table->text('link_chat')->nullable()->change();
      $table->string('trafego')->nullable()->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {}
};
