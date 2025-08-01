# Paquete para mostrar un modal en la parte inferior derecha de una app con laravel

## Descripción

Este paquete se debe comunicar con una API a través de http para obtener el estado de la suscripción. Se enviará un token alojado en el .env para saber cuando es la fecha final del periodo pagado (por lo general 1 mes).

En la parte inferior de la pantalla se debe mostrar un badge con los días restantes y al dar clic un modal con los detalles de la suscripción y el historial de facturas. 

Calcular los días restantes tomando la fecha actual como referencia para que cuando falten 5 días mostrar en naranja, cuando falten 2 días mostrar en rojo. El resto del tiempo en verde.

Si llega a tener 0 días disponibles mostrar un modal overlay que bloquea la aplicación. Añadir un botón que permita verificar el estado de la suscripción para quitarlo en caso de haber recibido el pago.

Este proyecto usará el sistema de Service Provider para que se auto registre el plugin / paquete en cualquier aplicación laravel instalada. También debe ser compatible con aplicaciones de FilamentPHP desde la versión 2 en adelante. En este proyecto usar Livewire y su mecanismo de componentes.

## Características

- ✅ Componente unificado (badge, modal y overlay en uno solo)
- ✅ Badge flotante en la esquina inferior derecha
- ✅ Modal con detalles de suscripción
- ✅ Colores dinámicos según días restantes
- ✅ Modal overlay bloqueante cuando expira (no se puede cerrar)
- ✅ Compatible con FilamentPHP v2+
- ✅ Compatible con Livewire 2
- ✅ Compatible con Laravel 10, 11 y 12
- ✅ Auto-registro con Service Provider
- ✅ Configuración vía .env

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
SUBSCRIPTION_API_URL=https://api.tudominio.com/subscription
SUBSCRIPTION_API_TOKEN=tu_token_aqui
SUBSCRIPTION_CHECK_INTERVAL=300
```

### 4. Incluir el componente en tu layout

En tu layout principal (ej: `resources/views/layouts/app.blade.php`):

```php
@livewire('subscription-modal::subscription-component')
```

## Configuración

### Variables de entorno

| Variable | Descripción | Por defecto |
|----------|-------------|-------------|
| `SUBSCRIPTION_API_URL` | URL de la API de suscripción | - |
| `SUBSCRIPTION_API_TOKEN` | Token para autenticación | - |
| `SUBSCRIPTION_CHECK_INTERVAL` | Intervalo de verificación en segundos | 300 |

### Configuración avanzada

Puedes personalizar el comportamiento editando `config/subscription-modal.php`:

```php
return [
    'api_url' => env('SUBSCRIPTION_API_URL'),
    'api_token' => env('SUBSCRIPTION_API_TOKEN'),
    'check_interval' => env('SUBSCRIPTION_CHECK_INTERVAL', 300),
    'warning_days' => 5,
    'critical_days' => 2,
    'position' => 'bottom-right', // bottom-right, bottom-left, top-right, top-left
];
```

## Compatibilidad

Este paquete es compatible con:

- **Laravel**: 10.x, 11.x, 12.x
- **Livewire**: 2.x
- **FilamentPHP**: 2.x, 3.x
- **PHP**: 8.1+

El paquete está optimizado para Livewire 2:

- **Eventos**: Usa `emit()` para eventos y `protected $listeners`
- **Componente unificado**: Badge, modal y overlay en un solo componente
- **Rendimiento**: Código simplificado y optimizado

## Uso

El paquete se auto-registra y funciona automáticamente. Los componentes principales son:

- `SubscriptionComponent`: Componente unificado que incluye badge, modal y overlay

### Comportamiento cuando la suscripción expira:

- **Modal no cerrable**: Cuando la suscripción está expirada (0 días restantes), el modal no se puede cerrar
- **Sin botón de cerrar**: El botón de cerrar desaparece cuando está expirado
- **Sin clic fuera**: No se puede cerrar haciendo clic fuera del modal
- **Botón "Verificar Pago"**: Solo se puede actualizar el estado de la suscripción
- **Diseño de advertencia**: El modal cambia a un diseño de advertencia con colores rojos

## API Response

El backend debe devolver el estado de la suscripción en un objeto JSON:

```json
{
  "user": "John Doe",
  "license_status": [
    {
      "database": "db1",
      "license_status": {
        "dias": 30,
        "overdue": false,
        "last_payment_date": "2025-05-05",
        "license_type": "1 sucursal",
        "invoices": [
          {
            "start_date": "2025-01-01",
            "end_date": "2025-01-31",
            "amount": 25
          }
        ]
      }
    },
    {
      "database": "db2",
      "license_status": "No active subscription found"
    }
  ]
}
```

## Plan de Trabajo

### Fase 1: Estructura del Paquete
- [x] Crear estructura de directorios
- [ ] Configurar composer.json
- [ ] Crear Service Provider
- [ ] Configurar auto-registro

### Fase 2: Componentes Livewire
- [ ] SubscriptionBadge component
- [ ] SubscriptionModal component  
- [ ] SubscriptionOverlay component
- [ ] Estilos CSS/JS

### Fase 3: Servicios y Lógica
- [ ] SubscriptionService para API calls
- [ ] Cálculo de días restantes
- [ ] Lógica de colores
- [ ] Cache de respuestas

### Fase 4: Integración FilamentPHP
- [ ] Detectar si Filament está instalado
- [ ] Integrar con layouts de Filament
- [ ] Compatibilidad con temas

### Fase 5: Testing y Documentación
- [ ] Tests unitarios
- [ ] Tests de integración
- [ ] Documentación completa
- [ ] Ejemplos de uso

## Desarrollo Local

Para desarrollo local, puedes usar:

```bash
# En tu proyecto de prueba
composer config repositories.subscription-modal path /ruta/al/paquete
composer require tu-namespace/laravel-subscription-modal:dev-master
```

## Licencia

MIT License