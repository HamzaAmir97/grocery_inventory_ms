<?php

$configuredAllowedOrigins = array_values(array_filter(array_map(
    fn (string $origin): string => rtrim(trim($origin), '/'),
    explode(',', (string) env('DASHBOARD_ALLOWED_ORIGINS', 'http://localhost:3000'))
)));

$isLocal = in_array((string) env('APP_ENV', 'production'), ['local', 'testing'], true);

$localFallbacks = $isLocal
    ? ['http://localhost:8000', 'http://127.0.0.1:8000', 'http://localhost:3000']
    : [];

$dashboardAllowedOrigins = array_values(array_unique(array_merge($configuredAllowedOrigins, $localFallbacks)));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => ['api/*', 'docs', 'api/documentation'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => $dashboardAllowedOrigins,

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Authorization', 'Content-Type', 'Accept', 'X-Request-Id', 'X-Requested-With'],

    'exposed_headers' => ['X-Request-Id'],

    'max_age' => 600,

    'supports_credentials' => false,

];
