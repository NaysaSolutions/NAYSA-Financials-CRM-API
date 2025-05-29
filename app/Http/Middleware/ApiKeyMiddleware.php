<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Define your API key (store this in .env for security)
        $validApiKey = env('API_KEY');

        // Get the API key from the request header
        $apiKey = $request->header('X-API-KEY');

        // Check if the API key is valid
        if (!$apiKey || $apiKey !== $validApiKey) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
