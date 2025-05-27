<?php

namespace App\Http\Middleware;

use App\Models\Receptioner;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceptionerEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->input('email');
        $doctor = Receptioner::where('email', $request->input('email'))->first();

        if (!$doctor) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if (is_null($doctor->email_verified_at)) {
            return response()->json([
                'message' => 'Please verify your email before you can login',
            ], 403);
        }
        return $next($request);
    }
}
