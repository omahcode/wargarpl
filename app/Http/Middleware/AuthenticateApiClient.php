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
        $clientId = $request->header('X-Client-ID');
        $clientSecret = $request->header('X-Client-Secret');

        if (!$clientId || !$clientSecret) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial API tidak lengkap (Header X-Client-ID dan X-Client-Secret wajib diisi).'
            ], 401);
        }

        // Fetch client
        $client = ApiClient::where('client_id', $clientId)->first();

        if (!$client || $client->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial API tidak valid atau aplikasi dinonaktifkan.'
            ], 401);
        }

        // Verify Secret Token
        if (!Hash::check($clientSecret, $client->secret_token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kredensial API tidak valid.'
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
        $cacheKey = "api_rate_limit:{$clientId}:" . date('Y-m-d-H-i');
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
