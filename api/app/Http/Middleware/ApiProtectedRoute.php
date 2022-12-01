<?php

namespace App\Http\Middleware;

use App\Models\JwtPermission;
use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class ApiProtectedRoute extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $token = explode(' ', $request->header('Authorization'))[1];
            $jwtPermission = JwtPermission::where('token', $token)->where('local', 'user')->count();
            if (!$jwtPermission) {
                return response()->json(['error' => 'Locate Unauthorized'], 401);
            }
            if (!$user) {
                return response()->json(['error' => 'Authorization is Invalid'], 401);
            }

        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['status' => 'Authorization is Invalid']);
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['status' => 'Authorization Expired']);
            } else{
                return response()->json(['status' => 'Authorization Token not found']);
            }
        }
        return $next($request);
    }
}
