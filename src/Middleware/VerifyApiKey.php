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
            $authUser = $request->getUser();
            $authPassword = $request->getPassword();

            if ($authUser === 'cron' && $authPassword) {
                $apiKey = $authPassword;
            }
        }

        if (!$apiKey) {
            return response()->json([
                'error' => 'API key missing. Provide via API-Key header or HTTP Basic Auth (user: cron, password: api-key)'
            ], 401);
        }

        $validKey = ApiKey::where('api_key', $apiKey)->where('is_active', true)->exists();

        if (!$validKey) {
            return response()->json(['error' => 'API key invalid'], 401);
        }

        return $next($request);
    }
} 