<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('newcorban_consultas', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('api_id')->unique();
      $table->string('tipo');
      $table->timestamp('api_created_at')->nullable(); // campo created_at da API
      $table->timestamp('api_finished_at')->nullable(); // campo finished_at da API
      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('newcorban_consultas');
  }
};
