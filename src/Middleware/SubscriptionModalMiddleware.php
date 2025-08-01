<?php

namespace LaravelSubscriptionModal\Middleware;

use Closure;
use Illuminate\Http\Request;
use LaravelSubscriptionModal\Services\SubscriptionService;

class SubscriptionModalMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Solo procesar respuestas HTML
        if (!$response->headers->get('content-type') || 
            !str_contains($response->headers->get('content-type'), 'text/html')) {
            return $response;
        }

        // Verificar si la configuración permite auto-inclusión
        if (!config('subscription-modal.auto_include', true)) {
            return $response;
        }

        // Verificar si el servicio está configurado
        $service = new SubscriptionService();
        if (!$service->isConfigured()) {
            return $response;
        }

        // Verificar si la ruta debe ser excluida
        $excludePatterns = config('subscription-modal.auto_include_exclude_routes', []);
        foreach ($excludePatterns as $pattern) {
            if ($request->is($pattern)) {
                return $response;
            }
        }

        // Obtener el contenido de la respuesta
        $content = $response->getContent();

        // Buscar el cierre del body tag
        $bodyClosePos = strripos($content, '</body>');
        
        if ($bodyClosePos !== false) {
            // Insertar el componente antes del cierre del body
            $componentHtml = '@livewire("subscription-modal::subscription-component")';
            $newContent = substr($content, 0, $bodyClosePos) . 
                         "\n    " . $componentHtml . "\n    " . 
                         substr($content, $bodyClosePos);
            
            $response->setContent($newContent);
        }

        return $response;
    }
} 