<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::dropIfExists('contratos');
    Schema::dropIfExists('simulacoes');
    Schema::dropIfExists('semsaldo');
    Schema::dropIfExists('contratos');
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {}
};
