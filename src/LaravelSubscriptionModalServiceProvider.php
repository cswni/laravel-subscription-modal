<?php

namespace LaravelSubscriptionModal;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use LaravelSubscriptionModal\Livewire\SubscriptionComponent;
use LaravelSubscriptionModal\Console\RegisterSubscriptionComponentsCommand;

class LaravelSubscriptionModalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/subscription-modal.php', 'subscription-modal'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publicar configuración
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/subscription-modal.php' => config_path('subscription-modal.php'),
            ], 'subscription-modal-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/subscription-modal'),
            ], 'subscription-modal-views');

            $this->publishes([
                __DIR__.'/../resources/css' => resource_path('css/vendor/subscription-modal'),
            ], 'subscription-modal-assets');

            $this->publishes([
                __DIR__.'/../resources/views/filament' => resource_path('views/vendor/subscription-modal/filament'),
            ], 'subscription-modal-filament');
        }

        // Registrar componentes Livewire con compatibilidad para v2 y v3
        $this->registerLivewireComponents();

        // Cargar vistas
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'subscription-modal');

        // Cargar CSS
        $this->loadCSS();

        // Auto-incluir el componente en todas las vistas
        $this->autoIncludeComponent();
        
        // Registrar middleware para auto-inclusión
        $this->registerMiddleware();

        // Detectar si Filament está instalado y registrar assets
        if (class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->registerFilamentAssets();
            $this->registerFilamentComponent();
            
            // Registrar el service provider específico de Filament
            $this->app->register(\LaravelSubscriptionModal\Filament\FilamentSubscriptionModalServiceProvider::class);
        }

        // Registrar comandos de consola
        if ($this->app->runningInConsole()) {
            $this->commands([
                RegisterSubscriptionComponentsCommand::class,
            ]);
        }

        // Registrar ruta de debug (solo en desarrollo)
        if (config('app.debug')) {
            \Route::get('/subscription-debug', function () {
                $service = new \LaravelSubscriptionModal\Services\SubscriptionService();
                return response()->json($service->getDebugInfo());
            })->middleware('web');

            \Route::get('/subscription-test', function () {
                return view('subscription-modal::subscription-component', [
                    'showModal' => true,
                    'remainingDays' => 30,
                    'badgeColor' => 'green',
                    'userInfo' => 'Test User',
                    'licenseStatus' => [
                        [
                            'database' => 'test_db',
                            'license_status' => [
                                'dias' => 30,
                                'overdue' => false,
                                'last_payment_date' => '2025-01-01',
                                'license_type' => 'Test License'
                            ]
                        ]
                    ]
                ]);
            })->middleware('web');
        }
    }

    /**
     * Registrar componentes Livewire (solo Livewire 2)
     */
    protected function registerLivewireComponents(): void
    {
        // Verificar si Livewire está disponible
        if (!class_exists(\Livewire\Livewire::class)) {
            return;
        }

        try {
            // Registrar componente unificado
            $components = [
                'subscription-modal::subscription-component' => SubscriptionComponent::class,
                'subscription-component' => SubscriptionComponent::class,
            ];

            foreach ($components as $name => $class) {
                try {
                    Livewire::component($name, $class);
                } catch (\Exception $e) {
                    // Log error but continue with other components
                    if (config('app.debug')) {
                        \Log::warning("Failed to register Livewire component {$name}: " . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            // Log error but don't break the application
            if (config('app.debug')) {
                \Log::error("Failed to register Livewire components: " . $e->getMessage());
            }
        }
    }



    /**
     * Cargar CSS del paquete
     */
    protected function loadCSS(): void
    {
        // Incluir CSS en el head de la aplicación
        if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
            \Illuminate\Support\Facades\Blade::directive('subscriptionModalStyles', function () {
                $cssPath = __DIR__ . '/../resources/css/subscription-modal.css';
                if (file_exists($cssPath)) {
                    $css = file_get_contents($cssPath);
                    return "<style>{$css}</style>";
                }
                return '';
            });
        }
    }

    /**
     * Auto-incluir el componente en todas las vistas
     */
    protected function autoIncludeComponent(): void
    {
        // Verificar si la configuración permite auto-inclusión
        if (!config('subscription-modal.auto_include', true)) {
            return;
        }

        // Verificar si el servicio está configurado
        $service = new \LaravelSubscriptionModal\Services\SubscriptionService();
        if (!$service->isConfigured()) {
            return;
        }

        // Registrar un View Composer que incluye el componente automáticamente
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            // Verificar si la vista debe ser excluida
            $excludePatterns = config('subscription-modal.auto_include_exclude_views', []);
            $viewName = $view->getName();
            
            foreach ($excludePatterns as $pattern) {
                if (fnmatch($pattern, $viewName)) {
                    return;
                }
            }

            // Solo incluir en vistas principales (no en componentes Livewire)
            if (str_contains($viewName, 'livewire::') || 
                str_contains($viewName, 'components.') ||
                str_contains($viewName, 'partials.')) {
                return;
            }

            // Agregar el componente al final del body usando una variable compartida
            $view->with('__subscription_modal_enabled', true);
        });

        // Crear un Blade directive para incluir el componente
        if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
            \Illuminate\Support\Facades\Blade::directive('subscriptionModal', function () {
                return '<?php if (isset($__subscription_modal_enabled) && $__subscription_modal_enabled): ?>
                    @livewire("subscription-modal::subscription-component")
                <?php endif; ?>';
            });

            // También crear un directive para incluir automáticamente en el final del body
            \Illuminate\Support\Facades\Blade::directive('subscriptionModalAuto', function () {
                return '<?php if (isset($__subscription_modal_enabled) && $__subscription_modal_enabled): ?>
                    @livewire("subscription-modal::subscription-component")
                <?php endif; ?>';
            });
        }
    }

    /**
     * Registrar middleware para auto-inclusión
     */
    protected function registerMiddleware(): void
    {
        // Registrar el middleware globalmente
        $this->app['router']->pushMiddlewareToGroup('web', \LaravelSubscriptionModal\Middleware\SubscriptionModalMiddleware::class);
    }

    /**
     * Registrar assets para FilamentPHP
     */
    protected function registerFilamentAssets(): void
    {
        // Aquí se pueden registrar assets específicos para Filament
        // Por ejemplo, estilos CSS personalizados para el tema de Filament
    }

    /**
     * Registrar componente para FilamentPHP
     */
    protected function registerFilamentComponent(): void
    {
        // Verificar si la configuración permite auto-inclusión
        if (!config('subscription-modal.auto_include', true)) {
            return;
        }

        // Verificar si el servicio está configurado
        $service = new \LaravelSubscriptionModal\Services\SubscriptionService();
        if (!$service->isConfigured()) {
            return;
        }

        // Registrar un View Composer específico para Filament
        \Illuminate\Support\Facades\View::composer([
            'filament::components.layouts.app',
            'filament::components.layouts.base',
            'filament::layouts.app',
            'filament::layouts.base',
            'filament-panels::components.layout.app',
            'filament-panels::components.layout.base',
        ], function ($view) {
            // Agregar el componente al final del body de Filament
            $view->with('__subscription_modal_enabled', true);
        });

        // Crear un Blade directive específico para Filament
        if (class_exists(\Illuminate\Support\Facades\Blade::class)) {
            \Illuminate\Support\Facades\Blade::directive('subscriptionModalFilament', function () {
                return '<?php if (isset($__subscription_modal_enabled) && $__subscription_modal_enabled): ?>
                    @livewire("subscription-modal::subscription-component")
                <?php endif; ?>';
            });
        }
    }
} 