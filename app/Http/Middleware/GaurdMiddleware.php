<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GaurdMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $guards): Response
    {
        $auth = false;

        foreach ($guards as $guard)
            if (Auth::guard($guard)->check()) {
                $auth = true;
                break;
            }

        if ($auth) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized access'], 401);
    }
}
