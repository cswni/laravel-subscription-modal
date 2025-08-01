<?php

namespace LaravelSubscriptionModal\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{
    protected string $apiUrl;
    protected string $apiToken;
    protected bool $cacheEnabled;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->apiUrl = config('subscription-modal.api_url');
        $this->apiToken = config('subscription-modal.api_token');
        $this->cacheEnabled = config('subscription-modal.cache_enabled', false);
        $this->cacheTtl = config('subscription-modal.cache_ttl', 300);
    }

    /**
     * Obtener el estado de la suscripción
     */
    public function getSubscriptionStatus(): ?array
    {
        $cacheKey = 'subscription_status_' . md5($this->apiToken);

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ])->timeout(10)->get($this->apiUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($this->cacheEnabled) {
                    Cache::put($cacheKey, $data, $this->cacheTtl);
                }

                return $data;
            }

            Log::error('Subscription API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Subscription API exception', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Verificar si la suscripción está activa
     */
    public function isSubscriptionActive(): bool
    {
        $status = $this->getSubscriptionStatus();
        
        if (!$status) {
            return false;
        }

        if (!isset($status['license_status']) || !is_array($status['license_status'])) {
            return false;
        }

        // Check if any database has an active subscription
        foreach ($status['license_status'] as $index => $dbStatus) {
            try {
                if (!is_array($dbStatus)) {
                    continue;
                }

                if (!isset($dbStatus['license_status'])) {
                    continue;
                }

                $licenseStatus = $dbStatus['license_status'];
                
                // Handle case where license_status is a string
                if (is_string($licenseStatus)) {
                    continue;
                }

                if (!is_array($licenseStatus)) {
                    continue;
                }

                if (isset($licenseStatus['dias']) && $licenseStatus['dias'] > 0 && !($licenseStatus['overdue'] ?? false)) {
                    return true;
                }
            } catch (\Exception $e) {
                Log::error("Error checking subscription status at index {$index}", [
                    'error' => $e->getMessage(),
                    'dbStatus' => $dbStatus
                ]);
            }
        }

        return false;
    }

    /**
     * Obtener días restantes (mínimo entre todas las bases de datos)
     */
    public function getRemainingDays(): int
    {
        $status = $this->getSubscriptionStatus();
        
        if (!$status) {
            Log::warning('Subscription status is null');
            return 0;
        }

        if (!isset($status['license_status'])) {
            Log::warning('license_status key not found in subscription response');
            return 0;
        }

        if (!is_array($status['license_status'])) {
            Log::warning('license_status is not an array', ['license_status' => $status['license_status']]);
            return 0;
        }

        $minDays = PHP_INT_MAX;
        $hasActiveSubscription = false;

        foreach ($status['license_status'] as $index => $dbStatus) {
            try {
                if (!is_array($dbStatus)) {
                    Log::warning("Database status at index {$index} is not an array", ['dbStatus' => $dbStatus]);
                    continue;
                }

                if (!isset($dbStatus['license_status'])) {
                    Log::warning("license_status key not found in database status at index {$index}");
                    continue;
                }

                $licenseStatus = $dbStatus['license_status'];
                
                // Handle case where license_status is a string (e.g., "No active subscription found")
                if (is_string($licenseStatus)) {
                    Log::info("License status is a string at index {$index}", ['status' => $licenseStatus]);
                    continue;
                }

                if (!is_array($licenseStatus)) {
                    Log::warning("License status at index {$index} is not an array", ['licenseStatus' => $licenseStatus]);
                    continue;
                }

                if (isset($licenseStatus['dias'])) {
                    $hasActiveSubscription = true;
                    $minDays = min($minDays, (int)$licenseStatus['dias']);
                }
            } catch (\Exception $e) {
                Log::error("Error processing database status at index {$index}", [
                    'error' => $e->getMessage(),
                    'dbStatus' => $dbStatus
                ]);
            }
        }

        return $hasActiveSubscription ? $minDays : 0;
    }

    /**
     * Obtener información del usuario
     */
    public function getUserInfo(): ?string
    {
        $status = $this->getSubscriptionStatus();
        
        if (!$status) {
            return null;
        }
        
        return $status['user'] ?? null;
    }

    /**
     * Obtener estado de licencias por base de datos
     */
    public function getLicenseStatus(): ?array
    {
        $status = $this->getSubscriptionStatus();
        
        if (!$status) {
            return null;
        }
        
        if (!isset($status['license_status']) || !is_array($status['license_status'])) {
            return null;
        }
        
        return $status['license_status'];
    }

    /**
     * Obtener el color del badge basado en los días restantes
     */
    public function getBadgeColor(): string
    {

        $days = $this->getRemainingDays();
        $criticalDays = config('subscription-modal.critical_days', 0);
        $warningDays = config('subscription-modal.warning_days', 5);

        if ($days <= 0) {
            return 'red';
        }

        if ($days <= $criticalDays) {
            return 'red';
        }

        if ($days <= $warningDays) {
            return 'orange';
        }

        return 'green';
    }

    /**
     * Limpiar cache
     */
    public function clearCache(): void
    {
        $cacheKey = 'subscription_status_' . md5($this->apiToken);
        Cache::forget($cacheKey);
    }

    /**
     * Verificar si la configuración es válida
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->apiToken);
    }

    /**
     * Obtener la fecha más alta válida de las facturas de una licencia
     */
    public function getHighestValidDate(array $licenseData): ?string
    {
        if (!isset($licenseData['invoices']) || !is_array($licenseData['invoices'])) {
            return null;
        }

        $highestDate = null;
        $highestEndDate = null;

        foreach ($licenseData['invoices'] as $invoice) {
            if (!isset($invoice['end_date'])) {
                continue;
            }

            $endDate = $invoice['end_date'];
            
            // Parse the date to ensure it's valid
            try {
                $parsedDate = \Carbon\Carbon::parse($endDate);
                
                // If this is the first valid date or it's later than the current highest
                if ($highestEndDate === null || $parsedDate->gt($highestEndDate)) {
                    $highestEndDate = $parsedDate;
                    $highestDate = $endDate;
                }
            } catch (\Exception $e) {
                // Skip invalid dates
                continue;
            }
        }

        return $highestDate;
    }

    /**
     * Obtener información de debug para troubleshooting
     */
    public function getDebugInfo(): array
    {
        $status = $this->getSubscriptionStatus();
        
        return [
            'configured' => $this->isConfigured(),
            'api_url' => $this->apiUrl,
            'has_token' => !empty($this->apiToken),
            'status_exists' => $status !== null,
            'status_structure' => $status ? array_keys($status) : null,
            'license_status_exists' => $status && isset($status['license_status']),
            'license_status_type' => $status && isset($status['license_status']) ? gettype($status['license_status']) : null,
            'license_status_count' => $status && isset($status['license_status']) && is_array($status['license_status']) ? count($status['license_status']) : null,
        ];
    }
} 