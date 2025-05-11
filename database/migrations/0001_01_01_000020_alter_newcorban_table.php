<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('newcorban_fgts', function (Blueprint $table) {
      $table->date('data');
    });
    Schema::table('newcorban_queue', function (Blueprint $table) {
      $table->date('data');
    });
  }

  public function down(): void {}
};
