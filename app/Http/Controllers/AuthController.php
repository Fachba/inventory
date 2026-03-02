<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LOGIN
    |--------------------------------------------------------------------------
    */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('user_email', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'User inactive'], 403);
        }

        $token = $this->generateToken($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('app.jwt_ttl') * 60
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE TOKEN
    |--------------------------------------------------------------------------
    */
    private function generateToken($user)
    {
        $issuedAt   = time();
        $ttl = (int) config('app.jwt_ttl');

        $expire = $issuedAt + ($ttl * 60);

        $payload = [
            'iss' => config('app.name'),
            'sub' => $user->user_id,
            'iat' => $issuedAt,
            'nbf' => $issuedAt,
            'exp' => $expire,
            'data' => [
                'user_id' => $user->user_id,
                'user_name' => $user->user_name,
                'role_id' => $user->role_id
            ]
        ];

        return JWT::encode(
            $payload,
            config('app.jwt_secret'),
            'HS256'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    public function logout()
    {
        // JWT stateless → logout hanya di client (hapus token)
        return response()->json([
            'message' => 'Logout success (remove token on client)'
        ]);
    }
}
