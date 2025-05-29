<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BearerTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Fetch the fixed secret key from .env
        $secretKey = env('SECRET_KEY', '3sjjlyUVrvO6lP8+fCqsp8PQ6fY27cJnd5HIE22wYrg');

        // Extract the Bearer Token from the Authorization header
        $authorizationHeader = $request->header('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = substr($authorizationHeader, 7); // Remove "Bearer " from the header

        if ($token !== $secretKey) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
