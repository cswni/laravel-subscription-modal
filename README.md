# Laravel Subscription Modal Package

## Descripción

Este paquete de Laravel se comunica con una API HTTP para obtener el estado de la suscripción y mostrar un modal de suscripción en la parte inferior derecha de la aplicación. Utiliza un token de Sanctum almacenado en el `.env` para autenticarse con la API.

El paquete muestra un badge flotante con los días restantes de suscripción. Al hacer clic en el badge, se abre un modal con los detalles de la suscripción y el historial de facturas. Los colores del badge cambian según los días restantes: verde (normal), naranja (advertencia), rojo (crítico).

Si la suscripción expira (0 días restantes), se muestra un modal overlay bloqueante que no se puede cerrar hasta que se verifique el pago.

## Características

- ✅ Componente unificado (badge, modal y overlay en uno solo)
- ✅ Badge flotante en la esquina inferior derecha
- ✅ Modal con detalles de suscripción e historial de facturas
- ✅ Colores dinámicos según días restantes (verde, naranja, rojo)
- ✅ Modal overlay bloqueante cuando expira (no se puede cerrar)
- ✅ Compatible con FilamentPHP v2+
- ✅ Compatible con Livewire 2
- ✅ Compatible con Laravel 10, 11 y 12
- ✅ Auto-registro con Service Provider
- ✅ Configuración vía .env
- ✅ Cache de respuestas API
- ✅ Manejo de errores robusto
- ✅ Diseño moderno con efectos visuales

## Instalación

### 1. Instalar el paquete

```bash
composer require tu-namespace/laravel-subscription-modal
```

### 2. Publicar la configuración (opcional)

```bash
php artisan vendor:publish --provider="TuNamespace\LaravelSubscriptionModal\LaravelSubscriptionModalServiceProvider"
```

### 3. Configurar variables de entorno

Añadir en tu archivo `.env`:

```env
SUBSCRIPTION_API_URL=https://dbbk.officenet.pro/api/check-license
SUBSCRIPTION_API_TOKEN=tu_token_sanctum_aqui
SUBSCRIPTION_CHECK_INTERVAL=300
```

**Nota**: El token debe ser un token de Sanctum válido para autenticarse con la API.

### 4. Incluir el componente en tu layout (Opcional)

**Auto-inclusión (Recomendado)**: El componente se incluye automáticamente en todas las vistas. No necesitas hacer nada más.

**Inclusión manual**: Si prefieres controlar dónde aparece el componente, puedes incluirlo manualmente en tu layout principal:

```php
<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
    @livewireStyles
</head>
<body>
    <!-- Tu contenido -->
    
    @livewire('subscription-modal::subscription-component')
    
    @livewireScripts
</body>
</html>
```

### 5. Para FilamentPHP

**Auto-inclusión automática**: El componente se incluye automáticamente en aplicaciones FilamentPHP. No necesitas hacer nada más.

**Inclusión manual**: Si prefieres controlar manualmente la inclusión, puedes:

1. Publicar las vistas de Filament:
```bash
php artisan vendor:publish --tag=subscription-modal-filament
```

2. Incluir el componente en tu layout de Filament:
```php
// En resources/views/vendor/filament/components/layouts/app.blade.php
@include('vendor.subscription-modal.filament.components.subscription-modal')
```

O usar el componente Livewire directamente:
```php
@livewire('subscription-modal::subscription-component')
```

## Configuración

### Variables de entorno

| Variable | Descripción | Por defecto | Requerido |
|----------|-------------|-------------|-----------|
| `SUBSCRIPTION_API_URL` | URL de la API de verificación de licencia | - | ✅ |
| `SUBSCRIPTION_API_TOKEN` | Token de Sanctum para autenticación | - | ✅ |
| `SUBSCRIPTION_CHECK_INTERVAL` | Intervalo de verificación en segundos | 300 | ❌ |
| `SUBSCRIPTION_AUTO_INCLUDE` | Auto-incluir el componente en todas las vistas | true | ❌ |

### Configuración avanzada

Puedes personalizar el comportamiento editando `config/subscription-modal.php`:

```php
return [
    'api_url' => env('SUBSCRIPTION_API_URL'),
    'api_token' => env('SUBSCRIPTION_API_TOKEN'),
    'check_interval' => env('SUBSCRIPTION_CHECK_INTERVAL', 300),
    'warning_days' => 5,        // Días para mostrar advertencia (naranja)
    'critical_days' => 2,       // Días para mostrar crítico (rojo)
    'position' => 'bottom-right', // Posición del badge
    'auto_include' => true,     // Auto-incluir el componente
    'auto_include_exclude_routes' => [
        'auth/*',
        'login',
        'logout',
        'register',
        'password/*',
        'admin/*',
        'api/*',
    ],
];
```

## Compatibilidad

Este paquete es compatible con:

- **Laravel**: 10.x, 11.x, 12.x
- **Livewire**: 2.x (específicamente optimizado para Livewire 2)
- **FilamentPHP**: 2.x, 3.x
- **PHP**: 8.1+

### Características específicas de Livewire 2:

- **Eventos**: Usa `emit()` para eventos y `protected $listeners`
- **Componente unificado**: Badge, modal y overlay en un solo componente
- **Rendimiento**: Código simplificado y optimizado
- **Compatibilidad**: No compatible con Livewire 3

## Uso

El paquete se auto-registra y funciona automáticamente una vez configurado.

### Auto-inclusión del componente

Por defecto, el componente se incluye automáticamente en todas las vistas de tu aplicación. Esto significa que:

- **No necesitas** agregar manualmente `@livewire('subscription-modal::subscription-component')` en tus layouts
- El componente aparecerá automáticamente en todas las páginas
- Se excluyen automáticamente rutas como autenticación, API, etc.

#### Deshabilitar auto-inclusión

Si prefieres controlar manualmente dónde aparece el componente, puedes deshabilitar la auto-inclusión:

```env
SUBSCRIPTION_AUTO_INCLUDE=false
```

Y luego incluir manualmente el componente donde lo necesites:

```php
@livewire('subscription-modal::subscription-component')
```

#### Personalizar rutas excluidas

Puedes personalizar qué rutas se excluyen de la auto-inclusión en `config/subscription-modal.php`:

```php
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
```

### Comportamiento del badge:

- **Verde**: Días restantes > 5
- **Naranja**: Días restantes ≤ 5
- **Rojo**: Días restantes ≤ 2 o expirado

### Comportamiento cuando la suscripción expira:

- **Modal no cerrable**: Cuando la suscripción está expirada (0 días restantes), el modal no se puede cerrar
- **Sin botón de cerrar**: El botón de cerrar desaparece cuando está expirado
- **Sin clic fuera**: No se puede cerrar haciendo clic fuera del modal
- **Botón "Verificar Pago"**: Solo se puede actualizar el estado de la suscripción
- **Diseño de advertencia**: El modal cambia a un diseño de advertencia con colores rojos

## API Response

El backend debe devolver el estado de la suscripción en un objeto JSON con la siguiente estructura:

### Ejemplo de respuesta exitosa:

```json
{
  "user": "test@test.com",
  "license_status": [
    {
      "license_status": {
        "dias": 0,
        "overdue": true,
        "last_payment_date": "2024-12-10",
        "invoices": [
          {
            "id": 1,
            "end_date": "2025-08-10"
          },
          {
            "id": 2,
            "end_date": "2024-12-10"
          }
        ]
      }
    }
  ]
}
```

### Ejemplo de respuesta sin suscripción activa:

```json
{
  "user": "test@test.com",
  "license_status": [
    {
      "license_status": "No active subscription found"
    }
  ]
}
```

### Estructura de la respuesta:

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `user` | string | Email del usuario |
| `license_status` | array | Array de estados de licencias por base de datos |
| `license_status[].license_status` | object/string | Estado de la licencia o mensaje de error |
| `license_status[].license_status.dias` | integer | Días restantes de suscripción |
| `license_status[].license_status.overdue` | boolean | Si la suscripción está vencida |
| `license_status[].license_status.last_payment_date` | string | Fecha del último pago (YYYY-MM-DD) |
| `license_status[].license_status.invoices` | array | Array de facturas |
| `license_status[].license_status.invoices[].id` | integer | ID de la factura |
| `license_status[].license_status.invoices[].end_date` | string | Fecha de fin de la factura (YYYY-MM-DD) |

## Funcionalidades

### Badge flotante
- Muestra los días restantes de suscripción
- Colores dinámicos según el estado
- Posicionado en la esquina inferior derecha
- Clic para abrir modal de detalles

### Modal de detalles
- Información del usuario
- Estado de cada licencia/base de datos
- Días restantes
- Fecha del último pago
- Fecha válida más alta (de las facturas)
- Historial de facturas
- Botón para verificar pago

### Modal overlay (cuando expira)
- Bloquea toda la aplicación
- No se puede cerrar
- Diseño de advertencia
- Solo permite verificar el estado de pago

## Desarrollo Local

Para desarrollo local, puedes usar:

```bash
# En tu proyecto de prueba
composer config repositories.subscription-modal path /ruta/al/paquete
composer require tu-namespace/laravel-subscription-modal:dev-master
```

### Testing local:

1. Configura las variables de entorno en tu proyecto de prueba
2. Asegúrate de que la API esté disponible
3. El componente se auto-registrará y funcionará automáticamente

## Troubleshooting

### El badge no aparece:
- Verifica que las variables de entorno estén configuradas
- Revisa los logs de Laravel para errores de API
- Asegúrate de que el componente esté incluido en el layout

### "N/A" en la fecha válida más alta:
- Verifica que la API devuelva el array `invoices` con `end_date`
- Revisa que las fechas estén en formato válido (YYYY-MM-DD)

### Modal no se cierra cuando expira:
- Este es el comportamiento esperado para suscripciones vencidas
- Solo se puede cerrar verificando el pago

### El componente no aparece en FilamentPHP:
- Verifica que FilamentPHP esté instalado correctamente
- Asegúrate de que las variables de entorno estén configuradas
- Si usas Filament v3, el componente se incluye automáticamente
- Para Filament v2, puedes publicar las vistas: `php artisan vendor:publish --tag=subscription-modal-filament`
- Verifica que no haya conflictos de z-index con otros elementos de Filament

## Licencia

MIT License