<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{

    public const HOME = '/clientes-sem-saldo';
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
      date_default_timezone_set(config('app.timezone'));
      Blade::if('role', function ($role) {
        return auth()->check() && auth()->user()->role === $role;
      });
      /*DB::listen(function ($query) {
        Log::info("SQL Executado:", [
          'sql' => $query->sql,
          'bindings' => $query->bindings,
          'time' => $query->time
        ]);
      });*/
    }
}
