<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JwtPermission;
use App\Models\Users;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);
        $credentials['active'] = 1;
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $jwtPermission = new JwtPermission();
        $jwtPermission->token = $token;
        $jwtPermission->local = 'user';
        $jwtPermission->save();
        Users::where('id', auth('api')->user()['id'])->update(['last_access' => now()]);
        return $this->respondWithToken($token);
    }

    public function me()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        $token = auth('api')->refresh();
        $jwtPermission = new JwtPermission;
        $jwtPermission->token = $token;
        $jwtPermission->local = 'user';
        $jwtPermission->save();
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
