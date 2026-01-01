<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    // Gabungan: '*' lama + tambahan dari kamu
    'paths' => [
        '*',
        'api/*',
        'sanctum/csrf-cookie',
        'yasmin/*',
    ],

    'allowed_methods' => ['*'],

    // Gabungan: FRONTEND_URL (laravel), vite port 5173, dan port 3000
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'http://localhost:5173',
        'http://localhost:3000',
    ],

    'allowed_origins_patterns' => array_filter([
        // Allow all subdomains of APP_DOMAIN in production
        // e.g., ppdb.sma-mutiarainsannusantara.my.id
        env('APP_DOMAIN') ? '/^https?:\/\/([a-z0-9\-]+\.)?' . preg_quote(env('APP_DOMAIN'), '/') . '$/' : null,
    ]),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    // Tetap true karena kamu pakai Sanctum + session
    'supports_credentials' => true,
];
