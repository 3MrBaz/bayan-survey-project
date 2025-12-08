<?php

use Illuminate\Support\Str;

return [

    'driver' => env('SESSION_DRIVER', 'redis'),

    'lifetime' => (int) env('SESSION_LIFETIME', 120),

    'expire_on_close' => false,

    'encrypt' => false,

    'files' => storage_path('framework/sessions'),

    'connection' => env('SESSION_CONNECTION'),

    'table' => 'sessions',

    'store' => env('SESSION_STORE'),

    'lottery' => [2, 100],

    'cookie' => env(
        'SESSION_COOKIE',
        Str::slug(env('APP_NAME', 'laravel'), '_') . '_session'
    ),

    'path' => '/',

    // Domain is required for SameSite=None to work
    'domain' => env('SESSION_DOMAIN', null),

    // Must be true when SameSite=None
    'secure' => env('SESSION_SECURE_COOKIE', true),

    'http_only' => env('SESSION_HTTP_ONLY', true),

    // Chrome requires SameSite=None for cross-site cookies 
    'same_site' => env('SESSION_SAMESITE', 'none'),

    // Required when using Cloudflare + cross-site cookies
    'partitioned' => env('SESSION_PARTITIONED_COOKIE', true),
];
