<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ApiClient;
use App\Models\ApiLog;

class AuthenticateApiClient
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $scope = '*'): Response
    {
        $token = $request->bearerToken() 
            ?? $request->header('X-API-Token') 
            ?? $request->header('X-Token')
            ?? $request->query('token');

        $client = null;

        if ($token) {
            // 1. Direct match on token column or plain secret_token
            $client = ApiClient::where('token', $token)
                ->orWhere('secret_token', $token)
                ->first();

            // 2. Fallback check for hashed secret_token
            if (!$client) {
                $activeClients = ApiClient::where('status', 'active')
                    ->whereNotNull('secret_token')
                    ->get();
                foreach ($activeClients as $candidate) {
                    if (Hash::check($token, $candidate->secret_token)) {
                        $client = $candidate;
                        break;
                    }
                }
            }
        } else {
            // Fallback for legacy clients using X-Client-ID and X-Client-Secret
            $clientId = $request->header('X-Client-ID');
            $clientSecret = $request->header('X-Client-Secret');

            if ($clientId && $clientSecret) {
                $candidate = ApiClient::where('client_id', $clientId)->first();
                if ($candidate && Hash::check($clientSecret, $candidate->secret_token)) {
                    $client = $candidate;
                }
            }
        }

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Token API tidak valid atau tidak disertakan. Gunakan header Authorization: Bearer <token> atau X-API-Token: <token>.'
            ], 401);
        }

        if ($client->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Token API ini telah dinonaktifkan (Revoked).'
            ], 401);
        }

        // Check Scopes
        $clientScopes = $client->scopes ?? [];
        if ($scope !== '*' && !in_array($scope, $clientScopes) && !in_array('*', $clientScopes)) {
            return response()->json([
                'success' => false,
                'message' => "Aplikasi Anda tidak memiliki hak akses (scope) untuk data ini. Diperlukan scope: {$scope}."
            ], 403);
        }

        // Enforce Rate Limiting
        $rateLimit = $client->rate_limit ?? 60;
        $clientIdKey = $client->token ? substr(md5($client->token), 0, 16) : ($client->client_id ?? $client->id);
        $cacheKey = "api_rate_limit:{$clientIdKey}:" . date('Y-m-d-H-i');
        $requestsCount = Cache::remember($cacheKey, 60, function () {
            return 0;
        });

        if ($requestsCount >= $rateLimit) {
            return response()->json([
                'success' => false,
                'message' => "Terlalu banyak permintaan API. Batas rate limit Anda adalah {$rateLimit} request/menit."
            ], 429);
        }

        Cache::increment($cacheKey);

        // Share the api_client with the request
        $request->attributes->set('api_client', $client);

        // Execute request & measure duration
        $startTime = microtime(true);
        $response = $next($request);
        $durationMs = round((microtime(true) - $startTime) * 1000);

        // Log request metrics to database
        try {
            ApiLog::create([
                'api_client_id' => $client->id,
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'ip_address' => $request->ip(),
                'response_code' => $response->getStatusCode(),
                'duration_ms' => $durationMs
            ]);
        } catch (\Exception $e) {
            // Silently ignore log database errors to prevent API crashes
        }

        return $response;
    }
}
