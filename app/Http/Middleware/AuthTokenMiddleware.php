<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $authToken = $request->header('Authorization');

        if (!$authToken || $authToken !== session('auth_token')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}

