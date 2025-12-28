<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdminApi
{
    /**
     * Handle API request - harus return JSON
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek user sudah login (punya token valid)
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated. Token required.'
            ], 401);
        }

        // Cek role admin atau super_admin
        if (!in_array($request->user()->role, ['admin', 'super_admin'])) {
            return response()->json([
                'message' => 'Forbidden. Admin access only.'
            ], 403);
        }

        return $next($request);
    }
}