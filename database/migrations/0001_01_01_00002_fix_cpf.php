<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Util;

return new class extends Migration {
  public function up(): void {
    $registros = DB::table('propostas')->select('id', 'cpf')->get();

    foreach ($registros as $registro) {
      $cpfFormatado = Util::formatCpf($registro->cpf);
      DB::table('propostas')->where('id', $registro->id)->update([
        'cpf' => $cpfFormatado
      ]);
    }
  }
};
