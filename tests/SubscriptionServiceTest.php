<?php

namespace LaravelSubscriptionModal\Tests;

use LaravelSubscriptionModal\Services\SubscriptionService;
use Orchestra\Testbench\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;

class SubscriptionServiceTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            \LaravelSubscriptionModal\LaravelSubscriptionModalServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar para testing
        Config::set('subscription-modal.api_url', 'https://api.test.com/subscription');
        Config::set('subscription-modal.api_token', 'test_token');
        Config::set('subscription-modal.cache_enabled', false);
    }

    public function test_service_can_be_instantiated()
    {
        $service = new SubscriptionService();
        $this->assertInstanceOf(SubscriptionService::class, $service);
    }

    public function test_is_configured_returns_true_with_valid_config()
    {
        $service = new SubscriptionService();
        $this->assertTrue($service->isConfigured());
    }

    public function test_is_configured_returns_false_with_invalid_config()
    {
        Config::set('subscription-modal.api_url', '');
        Config::set('subscription-modal.api_token', '');
        
        $service = new SubscriptionService();
        $this->assertFalse($service->isConfigured());
    }

    public function test_get_badge_color_returns_correct_colors()
    {
        // Test green color (more than 5 days)
        $service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getRemainingDays'])
            ->getMock();
        $service->method('getRemainingDays')->willReturn(10);
        $this->assertEquals('green', $service->getBadgeColor());
        
        // Test orange color (5 days or less)
        $service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getRemainingDays'])
            ->getMock();
        $service->method('getRemainingDays')->willReturn(5);
        $this->assertEquals('orange', $service->getBadgeColor());
        
        // Test red color (2 days or less)
        $service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getRemainingDays'])
            ->getMock();
        $service->method('getRemainingDays')->willReturn(2);
        $this->assertEquals('red', $service->getBadgeColor());
        
        // Test red color (0 days)
        $service = $this->getMockBuilder(SubscriptionService::class)
            ->onlyMethods(['getRemainingDays'])
            ->getMock();
        $service->method('getRemainingDays')->willReturn(0);
        $this->assertEquals('red', $service->getBadgeColor());
    }

    public function test_clear_cache_works()
    {
        $service = new SubscriptionService();
        
        // Simular que hay cache
        $cacheKey = 'subscription_status_' . md5('test_token');
        Cache::put($cacheKey, ['test' => 'data'], 300);
        
        $this->assertTrue(Cache::has($cacheKey));
        
        $service->clearCache();
        
        $this->assertFalse(Cache::has($cacheKey));
    }
} 