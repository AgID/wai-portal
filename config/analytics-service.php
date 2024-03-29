<?php

return [
    'admin_token' => env('ANALYTICS_ADMIN_TOKEN'),
    'api_base_uri' => env('ANALYTICS_API_BASE_URL'),
    'ssl_verify' => env('ANALYTICS_API_SSL_VERIFY'),
    'public_url' => env('ANALYTICS_PUBLIC_URL'),
    'public_dashboard' => env('ANALYTICS_PUBLIC_DASHBOARD_ID'),
    'cron_archiving_enabled' => env('ANALYTICS_CRON_ARCHIVING_ENABLED'),
    'api_public_domain' => env('ANALYTICS_API_PUBLIC_DOMAIN'),
    'api_public_path' => env('ANALYTICS_API_PUBLIC_PATH'),
    'widgets_base_url' => env('ANALYTICS_WIDGETS_BASE_URL'),
];
