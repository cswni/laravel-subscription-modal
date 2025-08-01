<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para la API de suscripción
    |
    */
    'api_url' => env('SUBSCRIPTION_API_URL'),
    'api_token' => env('SUBSCRIPTION_API_TOKEN'),
    'check_interval' => env('SUBSCRIPTION_CHECK_INTERVAL', 300), // 5 minutos por defecto

    /*
    |--------------------------------------------------------------------------
    | Visual Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración visual del badge y modal
    |
    */
    'warning_days' => env('SUBSCRIPTION_WARNING_DAYS', 5),
    'critical_days' => env('SUBSCRIPTION_CRITICAL_DAYS', 0),
    'position' => env('SUBSCRIPTION_POSITION', 'bottom-right'), // bottom-right, bottom-left, top-right, top-left

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración de cache para las respuestas de la API
    |
    */
    'cache_enabled' => env('SUBSCRIPTION_CACHE_ENABLED', true),
    'cache_ttl' => env('SUBSCRIPTION_CACHE_TTL', 300), // 5 minutos

    /*
    |--------------------------------------------------------------------------
    | FilamentPHP Integration
    |--------------------------------------------------------------------------
    |
    | Configuración específica para FilamentPHP
    |
    */
    'filament' => [
        'enabled' => env('SUBSCRIPTION_FILAMENT_ENABLED', true),
        'auto_inject' => env('SUBSCRIPTION_FILAMENT_AUTO_INJECT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auto-include Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para auto-incluir el componente
    |
    */
    'auto_include' => env('SUBSCRIPTION_AUTO_INCLUDE', true),
    'auto_include_exclude_views' => [
        'auth.*',
        'errors.*',
        'vendor.*',
        'livewire.*',
    ],
    'auto_include_exclude_routes' => [
        'auth/*',
        'login',
        'logout',
        'register',
        'password/*',
        'admin/*',
        'api/*',
        'telescope/*',
        'horizon/*',
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Mode
    |--------------------------------------------------------------------------
    |
    | Modo debug para desarrollo
    |
    */
    'debug' => env('SUBSCRIPTION_DEBUG', false),
]; 