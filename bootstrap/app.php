<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Inertia Middleware
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \App\Http\Middleware\ContentSecurityPolicy::class,
        ]);
        // Sanctum
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
        // CSRF Exception for Yasmin Validation API
        $middleware->validateCsrfTokens(except: [
            'api/yasmin/validate',
            'api/*', // 
        ]);
        // Email Verification
        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
        // Admin Middleware
        $middleware->alias([
            // 'admin.role' => \App\Http\Middleware\CheckAdminRole::class,
            'is_admin_api' => \App\Http\Middleware\IsAdminApi::class,
            'is_super_admin_api' => \App\Http\Middleware\IsSuperAdminApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();