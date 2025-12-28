<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContentSecurityPolicy
{
    /**
     * Add Content Security Policy headers to prevent XSS attacks
     * Only applies in production (APP_DEBUG=false)
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip CSP in development mode (allows Vite dev server)
        if (config('app.debug')) {
            return $response;
        }

        // Skip CSP for API responses
        if ($request->is('api/*')) {
            return $response;
        }

        // Build CSP directives
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.googletagmanager.com https://www.google-analytics.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob: https:",
            "connect-src 'self' https://www.google-analytics.com",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ];

        // Add CSP header
        $response->headers->set(
            'Content-Security-Policy',
            implode('; ', $csp)
        );

        // Additional security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
