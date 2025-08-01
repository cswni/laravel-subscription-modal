# Desarrollo Local del Paquete

## Configuración para Desarrollo

### 1. Clonar el repositorio

```bash
git clone <tu-repositorio>
cd laravel-subscription-modal
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar para desarrollo local

En tu proyecto Laravel de prueba, añade el repositorio local:

```bash
# En tu proyecto de prueba
composer config repositories.subscription-modal path /ruta/completa/al/paquete
```

### 4. Instalar el paquete en modo desarrollo

```bash
# En tu proyecto de prueba
composer require laravel-subscription-modal/subscription-modal:dev-master
```

### 5. Configurar variables de entorno

En tu proyecto de prueba, añade al `.env`:

```env
SUBSCRIPTION_API_URL=https://api.tudominio.com/subscription
SUBSCRIPTION_API_TOKEN=tu_token_aqui
SUBSCRIPTION_CHECK_INTERVAL=300
SUBSCRIPTION_DEBUG=true
```

### 6. Incluir el componente

En tu layout principal (`resources/views/layouts/app.blade.php`):

```php
<!DOCTYPE html>
<html>
<head>
    <title>Mi Aplicación</title>
    @livewireStyles
</head>
<body>
    <!-- Tu contenido aquí -->
    
    @livewire('subscription-modal::subscription-badge')
    @livewireScripts
</body>
</html>
```

## Estructura del Proyecto

```
laravel-subscription-modal/
├── composer.json
├── config/
│   └── subscription-modal.php
├── src/
│   ├── LaravelSubscriptionModalServiceProvider.php
│   ├── Livewire/
│   │   ├── SubscriptionBadge.php
│   │   ├── SubscriptionModal.php
│   │   └── SubscriptionOverlay.php
│   ├── Services/
│   │   └── SubscriptionService.php
│   └── Traits/
│       └── LivewireCompatibility.php
├── resources/
│   ├── views/
│   │   ├── subscription-badge.blade.php
│   │   ├── subscription-modal.blade.php
│   │   └── subscription-overlay.blade.php
│   └── css/
│       └── subscription-modal.css
├── examples/
│   └── usage-example.blade.php
├── tests/
├── README.md
└── DEVELOPMENT.md
```

## Comandos Útiles

### Publicar configuración
```bash
php artisan vendor:publish --provider="LaravelSubscriptionModal\LaravelSubscriptionModalServiceProvider"
```

### Limpiar cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Verificar instalación
```bash
php artisan package:discover
```

## Testing

### Crear tests básicos

```bash
# En el directorio del paquete
mkdir -p tests
```

### Ejecutar tests

```bash
./vendor/bin/phpunit
```

## API de Prueba

Para desarrollo, puedes usar una API de prueba como JSONPlaceholder o crear un endpoint de prueba:

```php
// En tu proyecto de prueba, crear una ruta de prueba
Route::get('/api/test-subscription', function () {
    return response()->json([
        'dias' => 5,
        'overdue' => false,
        'owner' => 'Carlos Perez',
        'lastPaymentDate' => '2025-05-05',
        'licenceType' => '1 sucursal',
        'invoices' => [
            [
                'id' => '5a4sd54fa5sd4f',
                'startDate' => '2025-01-01',
                'endDate' => '2025-01-31',
                'amount' => 25
            ]
        ]
    ]);
});
```

Luego configurar en `.env`:
```env
SUBSCRIPTION_API_URL=http://localhost:8000/api/test-subscription
SUBSCRIPTION_API_TOKEN=test_token
```

## Debugging

### Habilitar debug
```env
SUBSCRIPTION_DEBUG=true
```

### Ver logs
```bash
tail -f storage/logs/laravel.log
```

### Verificar configuración
```bash
php artisan config:show subscription-modal
```

## FilamentPHP Integration

Si tu proyecto usa FilamentPHP, el paquete se auto-detecta y se integra automáticamente.

### Verificar integración
```bash
php artisan filament:install
```

### Personalizar layout de Filament
Crea el archivo `resources/views/vendor/filament/components/layouts/app.blade.php`:

```php
<x-filament-panels::layout.base :livewire="$this">
    @livewire('subscription-modal::subscription-badge')
    
    {{ $slot }}
</x-filament-panels::layout.base>
```

## Testing Compatibility

### Testing with Livewire 2
```bash
# En tu proyecto de prueba con Livewire 2
composer require livewire/livewire:^2.12
composer require laravel-subscription-modal/subscription-modal:dev-master
```

### Testing with Livewire 3
```bash
# En tu proyecto de prueba con Livewire 3
composer require livewire/livewire:^3.6.4
composer require laravel-subscription-modal/subscription-modal:dev-master
```

### Testing with different Laravel versions
```bash
# Laravel 10
composer create-project laravel/laravel:^10.0 test-project-10

# Laravel 11
composer create-project laravel/laravel:^11.0 test-project-11

# Laravel 12
composer create-project laravel/laravel:^12.0 test-project-12
```

### Event Dispatching Compatibility

El paquete maneja automáticamente las diferencias entre Livewire 2 y 3:

- **Livewire 2**: Usa `$this->emit('event-name')` y `protected $listeners`
- **Livewire 3**: Usa `$this->dispatch('event-name')` y `#[On]` attributes

El trait `LivewireCompatibility` proporciona el método `dispatchEvent()` que funciona en ambas versiones:

```php
// En lugar de usar dispatch() o emit() directamente
$this->dispatchEvent('openSubscriptionModal');
$this->dispatchEvent('showSubscriptionOverlay');
```

### Listener Syntax Differences

**Livewire 2:**
```php
protected $listeners = [
    'openSubscriptionModal' => 'open',
    'closeSubscriptionModal' => 'close',
];
```

**Livewire 3:**
```php
use Livewire\Attributes\On;

#[On('openSubscriptionModal')]
public function open() { }

#[On('closeSubscriptionModal')]
public function close() { }
```

## Troubleshooting

### El badge no aparece
1. Verificar que Livewire esté instalado
2. Verificar que el componente esté incluido en el layout
3. Verificar la configuración del .env
4. Revisar los logs de Laravel
5. Verificar la versión de Livewire instalada

### Error de API
1. Verificar la URL de la API
2. Verificar el token
3. Verificar que la API devuelva el formato correcto
4. Revisar los logs de Laravel

### Problemas con FilamentPHP
1. Verificar que Filament esté instalado correctamente
2. Verificar que el layout de Filament incluya el componente
3. Verificar los z-index en CSS

## Contribuir

1. Fork el repositorio
2. Crear una rama para tu feature
3. Hacer commit de tus cambios
4. Push a la rama
5. Crear un Pull Request

## Licencia

MIT License 