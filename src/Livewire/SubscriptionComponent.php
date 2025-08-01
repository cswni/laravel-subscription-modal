<?php

namespace LaravelSubscriptionModal\Livewire;

use Livewire\Component;
use LaravelSubscriptionModal\Services\SubscriptionService;

class SubscriptionComponent extends Component
{
    public bool $showModal = false;
    public bool $showOverlay = false;
    public ?array $subscriptionData = null;
    public int $remainingDays = 0;
    public string $badgeColor = 'green';
    public ?string $userInfo = null;
    public ?array $licenseStatus = null;
    public bool $isConfigured = false;

    protected $listeners = [
        'refreshSubscriptionData' => 'refreshData'
    ];

    private SubscriptionService $subscriptionService;

    public function boot(SubscriptionService $subscriptionService): void
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function mount(): void
    {
        $this->isConfigured = $this->subscriptionService->isConfigured();
        
        if ($this->isConfigured) {
            $this->loadSubscriptionData();
        }
    }

    public function openModal(): void
    {
        $this->loadSubscriptionData();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function refreshData(): void
    {
        $this->subscriptionService->clearCache();
        $this->loadSubscriptionData();
    }

    protected function loadSubscriptionData(): void
    {
        try {
            $this->subscriptionData = $this->subscriptionService->getSubscriptionStatus();
            $this->remainingDays = $this->subscriptionService->getRemainingDays();
            $this->badgeColor = $this->subscriptionService->getBadgeColor();
            $this->userInfo = $this->subscriptionService->getUserInfo();
            $this->licenseStatus = $this->subscriptionService->getLicenseStatus();
            
            // Process license data to include highest valid date
            if ($this->licenseStatus && is_array($this->licenseStatus)) {
                foreach ($this->licenseStatus as &$license) {
                    if (is_array($license) && isset($license['license_status']) && is_array($license['license_status'])) {
                        $license['highest_valid_date'] = $this->subscriptionService->getHighestValidDate($license['license_status']);
                    }
                }
            }
            
            // Show overlay if no days remaining
            $this->showOverlay = $this->remainingDays <= 0;
            
            // Debug logging
            if (config('app.debug')) {
                \Log::info('SubscriptionComponent: Data loaded successfully', [
                    'subscriptionData' => $this->subscriptionData ? 'exists' : 'null',
                    'remainingDays' => $this->remainingDays,
                    'userInfo' => $this->userInfo,
                    'licenseStatus' => $this->licenseStatus ? 'exists' : 'null',
                    'showOverlay' => $this->showOverlay
                ]);
            }
        } catch (\Exception $e) {
            if (config('app.debug')) {
                \Log::error('SubscriptionComponent: Error loading subscription data', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Set default values on error
            $this->subscriptionData = null;
            $this->remainingDays = 0;
            $this->badgeColor = 'red';
            $this->userInfo = null;
            $this->licenseStatus = null;
            $this->showOverlay = false;
        }
    }

    public function render()
    {
        $this->subscriptionService->clearCache();

        return view('subscription-modal::subscription-component');
    }

    public function getDebugInfo()
    {
        return $this->subscriptionService->getDebugInfo();
    }
} 