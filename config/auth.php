<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    
    'admin' => [  // ← PERBAIKI INI (salah tempat di config kamu)
        'driver' => 'session',
        'provider' => 'admins',  // ← Pakai provider 'admins'
    ],
],

'providers' => [
    'users' => [
        'driver' => 'eloquent',
        'model' => env('AUTH_MODEL', App\Models\User::class),
    ],

    'admins' => [  // ← PERBAIKI INI
        'driver' => 'eloquent',  // ← Bukan 'session', tapi 'eloquent'
        'model' => App\Models\Admin::class,
    ],
],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];