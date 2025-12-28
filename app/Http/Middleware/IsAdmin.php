<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login → redirect ke login
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Jika bukan admin atau super_admin → 403 Forbidden
        if (! in_array($request->user()->role, ['admin', 'super_admin'])) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
