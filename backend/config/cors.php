<?php

// 1. Default allowed origins across production and local frontend development
$defaultOrigins = [
    'https://serv5.com.eg',
    'https://www.serv5.com.eg',
    'http://localhost:3000',
    'http://127.0.0.1:3000',
    'http://localhost:3001',
    'http://127.0.0.1:3001',
];

// 2. Read additional origins from .env (ignoring wildcard '*' since supports_credentials is true)
$envOrigins = array_values(array_filter(
    array_map('trim', explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))),
    static fn (string $origin): bool => $origin !== '' && $origin !== '*'
));

// Merge without duplicates
$allowedOrigins = array_values(array_unique(array_merge($defaultOrigins, $envOrigins)));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout',
        'register',
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $allowedOrigins,

    'allowed_origins_patterns' => [
        '#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#',
    ],

    'allowed_headers' => [
        'Accept',
        'Accept-Language',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-Requested-With',
        'X-XSRF-TOKEN',
        'X-Socket-ID',
    ],

    'exposed_headers' => [
        'Authorization',
    ],

    'max_age' => 86400,

    'supports_credentials' => true,

];
