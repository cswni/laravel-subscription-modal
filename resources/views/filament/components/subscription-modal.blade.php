@if(config('subscription-modal.auto_include', true))
    @php
        $service = new \LaravelSubscriptionModal\Services\SubscriptionService();
    @endphp
    
    @if($service->isConfigured())
        @livewire('subscription-modal::subscription-component')
        
        <style>
            /* Estilos específicos para Filament */
            .subscription-modal-badge {
                z-index: 9999 !important;
            }
            .subscription-modal-overlay {
                z-index: 10000 !important;
            }
            .subscription-modal-modal {
                z-index: 9998 !important;
            }
        </style>
    @endif
@endif 