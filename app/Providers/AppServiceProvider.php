<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
      /*DB::listen(function ($query) {
        Log::info("SQL Executado:", [
          'sql' => $query->sql,
          'bindings' => $query->bindings,
          'time' => $query->time
        ]);
      });*/
    }
}
