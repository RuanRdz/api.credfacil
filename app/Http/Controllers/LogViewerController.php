<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class LogViewerController extends Controller
{
  public function index()
  {
    $path = storage_path('logs/laravel.log');

    $log = File::exists($path) ? File::get($path) : 'Log vazio ou não encontrado.';

    return view('logs.index', [
      'log' => nl2br(e($log)), // escapando HTML e convertendo \n para <br>
    ]);
  }

  public function clear()
  {
    $path = storage_path('logs/laravel.log');

    File::put($path, ''); // limpa o log

    return redirect()->route('logs.index')->with('success', 'Log limpo com sucesso.');
  }
}
