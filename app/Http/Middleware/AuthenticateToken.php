<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthenticateToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token || !Cache::has("auth_token:$token")) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
