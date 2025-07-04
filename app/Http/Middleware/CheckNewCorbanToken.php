<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNewCorbanToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response {
    $token = $request->query('token');

    $expectedToken = env('NEWCORBAN_TOKEN');

    if (!$token || $token !== $expectedToken) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }

    return $next($request);
  }
}
