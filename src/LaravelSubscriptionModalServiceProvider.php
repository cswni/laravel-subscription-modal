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
        }

        // Registrar componentes Livewire con compatibilidad para v2 y v3
        $this->registerLivewireComponents();

        // Cargar vistas
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'subscription-modal');

        // Cargar CSS
        $this->loadCSS();

        // Detectar si Filament está instalado y registrar assets
        if (class_exists(\Filament\FilamentServiceProvider::class)) {
            $this->registerFilamentAssets();
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
     * Registrar assets para FilamentPHP
     */
    protected function registerFilamentAssets(): void
    {
        // Aquí se pueden registrar assets específicos para Filament
        // Por ejemplo, estilos CSS personalizados para el tema de Filament
    }
} 