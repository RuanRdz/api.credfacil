<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPrioraToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    $data = $request->json()->all();

    $token = $data['bot_context']['prioraToken'] ?? null;

    // Token esperado (pode estar no .env ou em config)
    $expectedToken = env('PRIORA_TOKEN');

    if (!$token || $token !== $expectedToken) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $next($request);
  }
}
