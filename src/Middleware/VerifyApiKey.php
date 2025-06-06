<?php

namespace Azuriom\Plugin\ApiExtender\Middleware;

use Closure;
use Illuminate\Http\Request;
use Azuriom\Plugin\ApiExtender\Models\ApiKey;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('API-Key');

        if (!$apiKey) {
            return response()->json(['error' => 'API key missing'], 401);
        }

        $validKey = ApiKey::where('api_key', $apiKey)->where('is_active', true)->exists();

        if (!$validKey) {
            return response()->json(['error' => 'API key invalid'], 401);
        }

        return $next($request);
    }
} 