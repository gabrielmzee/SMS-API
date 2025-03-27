<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class JwtAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if the token is present in the request
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 401);
            }

            // Decode the JWT token
            $payload = JWTAuth::setToken($token)->getPayload();

            // Check if 'userId' is part of the token
            if (!isset($payload['userId'])) {
                return response()->json(['error' => "'userId' field is required in the token"], 401);
            }

            // Get the token's issued time
            $issuedAt = Carbon::createFromTimestamp($payload['iat']);

            // Check if the token is older than 120 seconds (2 minutes)
            $currentTime = Carbon::now();
            if ($currentTime->diffInSeconds($issuedAt) > 120) {
                return response()->json(['error' => 'Token has expired'], 401);
            }

            // Allow the request to proceed
            return $next($request);

        } catch (Exception $e) {
            return response()->json(['error' => 'Unauthorized, invalid token or error'], 401);
        }
    }
}
