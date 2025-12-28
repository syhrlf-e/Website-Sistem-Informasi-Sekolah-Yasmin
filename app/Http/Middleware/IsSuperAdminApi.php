<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdminApi
{
    /**
     * Handle API request - hanya super admin
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek user sudah login
        if (!$request->user()) {
            return response()->json([
                'message' => 'Unauthenticated. Token required.'
            ], 401);
        }

        // Cek role super_admin
        if ($request->user()->role !== 'super_admin') {
            return response()->json([
                'message' => 'Forbidden. Super Admin access only.'
            ], 403);
        }

        return $next($request);
    }
}