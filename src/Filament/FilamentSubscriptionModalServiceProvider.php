<?php

namespace LaravelSubscriptionModal\Filament;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use LaravelSubscriptionModal\Services\SubscriptionService;

class FilamentSubscriptionModalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Verificar si la configuración permite auto-inclusión
        if (!config('subscription-modal.auto_include', true)) {
            return;
        }

        // Verificar si el servicio está configurado
        $service = new SubscriptionService();
        if (!$service->isConfigured()) {
            return;
        }

        // Inyectar el componente en Filament usando Filament Facade
        Filament::registerRenderHook(
            'body.end',
            static fn (): string => Blade::render('<livewire:subscription-modal::subscription-component />'),
        );

        // También registrar en el head para los estilos si es necesario
        Filament::registerRenderHook(
            'head.end',
            fn (): string => '<style>
                /* Estilos específicos para Filament */
                .subscription-modal-badge {
                    z-index: 9999 !important;
                }
                .subscription-modal-overlay {
                    z-index: 10000 !important;
                }
            </style>'
        );

        // Registrar un View Composer como respaldo para Filament
        \Illuminate\Support\Facades\View::composer([
            'filament::components.layouts.app',
            'filament::components.layouts.base',
            'filament::layouts.app',
            'filament::layouts.base',
            'filament-panels::components.layout.app',
            'filament-panels::components.layout.base',
            'filament-panels::components.layouts.app',
            'filament-panels::components.layouts.base',
        ], function ($view) {
            // Agregar el componente al final del body de Filament
            $view->with('__subscription_modal_enabled', true);
        });
    }
} 