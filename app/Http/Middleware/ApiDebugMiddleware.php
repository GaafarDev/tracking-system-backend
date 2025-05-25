<?php
// Create this file: app/Http/Middleware/ApiDebugMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiDebugMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Log incoming API requests
        if ($request->is('api/*')) {
            Log::info('=== API REQUEST START ===');
            Log::info('Method: ' . $request->method());
            Log::info('URL: ' . $request->fullUrl());
            Log::info('Headers: ' . json_encode($request->headers->all()));
            Log::info('Body: ' . $request->getContent());
            Log::info('User: ' . ($request->user() ? $request->user()->id : 'Not authenticated'));
            Log::info('=== API REQUEST END ===');
        }

        $response = $next($request);

        // Log API responses
        if ($request->is('api/*')) {
            Log::info('=== API RESPONSE START ===');
            Log::info('Status: ' . $response->getStatusCode());
            Log::info('Content: ' . $response->getContent());
            Log::info('=== API RESPONSE END ===');
        }

        return $response;
    }
}