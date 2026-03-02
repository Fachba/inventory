<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtMiddleware
{
    public function handle($request, Closure $next)
    {
        try {

            $header = $request->header('Authorization');

            if (!$header || !str_starts_with($header, 'Bearer ')) {
                return response()->json([
                    'message' => 'Token not provided'
                ], 401);
            }

            $token = str_replace('Bearer ', '', $header);

            $decoded = JWT::decode(
                $token,
                new Key(config('app.jwt_secret'), 'HS256')
            );

            // simpan user data ke request
            $request->auth = $decoded->data;
            // $request->merge(['auth' => $decoded->data]);
        } catch (Exception $e) {

            return response()->json([
                'message' => 'Invalid or expired token',
                'error' => $e->getMessage() // bisa dihapus di production
            ], 401);
        }

        return $next($request);
    }
}
