# Instalación en tu Proyecto de Prueba

## Compatibilidad

Este paquete es compatible con:
- **Laravel**: 10.x, 11.x, 12.x
- **Livewire**: 2.x, 3.x
- **FilamentPHP**: 2.x, 3.x
- **PHP**: 8.1+

## Paso a Paso para Probar el Paquete

### 1. Configurar el repositorio local en tu proyecto

En tu proyecto Laravel de prueba, ejecuta:

```bash
composer config repositories.subscription-modal path /d:/dev/invoicing-tool
```

### 2. Instalar el paquete

```bash
composer require laravel-subscription-modal/subscription-modal:dev-master
```

### 3. Configurar variables de entorno

Añade estas variables a tu archivo `.env`:

```env
SUBSCRIPTION_API_URL=https://api.tudominio.com/subscription
SUBSCRIPTION_API_TOKEN=tu_token_aqui
SUBSCRIPTION_CHECK_INTERVAL=300
SUBSCRIPTION_DEBUG=true
```

### 4. Crear una API de prueba (opcional)

Si no tienes una API real, puedes crear un endpoint de prueba en tu proyecto:

```php
// En routes/web.php o routes/api.php
Route::get('/api/test-subscription', function () {
    return response()->json([
        'user' => 'John Doe',
        'license_status' => [
            [
                'database' => 'db1',
                'license_status' => [
                    'dias' => 30,
                    'overdue' => false,
                    'last_payment_date' => '2025-05-05',
                    'license_type' => '1 sucursal',
                    'invoices' => [
                        [
                            'start_date' => '2025-01-01',
                            'end_date' => '2025-01-31',
                            'amount' => 25
                        ]
                    ]
                ]
            ],
            [
                'database' => 'db2',
                'license_status' => 'No active subscription found'
            ]
        ]
    ]);
});
```

Si usas la API de prueba, actualiza tu `.env`:

```env
SUBSCRIPTION_API_URL=http://localhost:8000/api/test-subscription
SUBSCRIPTION_API_TOKEN=test_token
```

### 5. Incluir el componente en tu layout

En tu archivo `resources/views/layouts/app.blade.php` (o el layout principal que uses):

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

### 6. Si usas FilamentPHP

Si tu proyecto usa FilamentPHP, crea el archivo:

```bash
mkdir -p resources/views/vendor/filament/components/layouts
```

Y crea `resources/views/vendor/filament/components/layouts/app.blade.php`:

```php
<x-filament-panels::layout.base :livewire="$this">
    @livewire('subscription-modal::subscription-badge')
    
    {{ $slot }}
</x-filament-panels::layout.base>
```

### 7. Limpiar cache y verificar

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### 8. Probar diferentes escenarios

Para probar diferentes estados, modifica el endpoint de prueba:

#### Escenario 1: Suscripción activa (verde)
```json
{
  "user": "John Doe",
  "license_status": [
    {
      "database": "db1",
      "license_status": {
        "dias": 15,
        "overdue": false,
        "last_payment_date": "2025-05-05",
        "license_type": "1 sucursal"
      }
    }
  ]
}
```

#### Escenario 2: Advertencia (naranja)
```json
{
  "user": "John Doe",
  "license_status": [
    {
      "database": "db1",
      "license_status": {
        "dias": 5,
        "overdue": false,
        "last_payment_date": "2025-05-05",
        "license_type": "1 sucursal"
      }
    }
  ]
}
```

#### Escenario 3: Crítico (rojo)
```json
{
  "user": "John Doe",
  "license_status": [
    {
      "database": "db1",
      "license_status": {
        "dias": 2,
        "overdue": false,
        "last_payment_date": "2025-05-05",
        "license_type": "1 sucursal"
      }
    }
  ]
}
```

#### Escenario 4: Expirado (overlay bloqueante)
```json
{
  "user": "John Doe",
  "license_status": [
    {
      "database": "db1",
      "license_status": {
        "dias": 0,
        "overdue": true,
        "last_payment_date": "2025-05-05",
        "license_type": "1 sucursal"
      }
    }
  ]
}
```

### 9. Verificar que funciona

1. Visita tu aplicación
2. Deberías ver un badge circular en la esquina inferior derecha
3. Haz clic en el badge para abrir el modal
4. Prueba el botón "Verificar Pago" en el overlay cuando esté expirado

### 10. Debugging

Si algo no funciona:

1. Verifica los logs: `tail -f storage/logs/laravel.log`
2. Verifica que Livewire esté instalado: `composer show livewire/livewire`
3. Verifica la configuración: `php artisan config:show subscription-modal`
4. Verifica que el componente esté registrado: `php artisan package:discover`

### 11. Personalización

Puedes personalizar los colores y posiciones editando `config/subscription-modal.php`:

```php
'warning_days' => 5,
'critical_days' => 2,
'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
```

## Comandos Útiles

```bash
# Verificar instalación
composer show laravel-subscription-modal/subscription-modal

# Limpiar cache
php artisan cache:clear

# Ver configuración
php artisan config:show subscription-modal

# Ver rutas
php artisan route:list | grep subscription
```

## Estructura Final

Tu proyecto debería tener:

```
tu-proyecto/
├── .env (con las variables de suscripción)
├── resources/views/layouts/app.blade.php (con @livewire)
├── routes/web.php (con endpoint de prueba opcional)
└── vendor/laravel-subscription-modal/ (paquete instalado)
```

¡Listo! El paquete debería funcionar automáticamente una vez configurado. 