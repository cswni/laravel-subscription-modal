# Troubleshooting Guide

## Common Issues and Solutions

### 1. ComponentNotFoundException: Unable to find component: [subscription-modal::subscription-badge]

This error occurs when Livewire cannot find the registered component. Here are the steps to fix it:

#### Step 1: Verify Package Installation
```bash
composer show laravel-subscription-modal/subscription-modal
```

#### Step 2: Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan optimize:clear
```

#### Step 3: Manually Register Components
```bash
php artisan subscription-modal:register-components
```

#### Step 4: Verify Service Provider Registration
Check if the service provider is registered in `config/app.php`:
```php
'providers' => [
    // ... other providers
    LaravelSubscriptionModal\LaravelSubscriptionModalServiceProvider::class,
],
```

If not, add it manually or run:
```bash
php artisan package:discover
```

#### Step 5: Check Livewire Installation
```bash
composer show livewire/livewire
```

Make sure Livewire is properly installed and configured.

#### Step 6: Alternative Component Names
Try using the component without the namespace:
```php
@livewire('subscription-badge')
```

Instead of:
```php
@livewire('subscription-modal::subscription-badge')
```

### 2. Service Provider Not Loading

If the service provider is not loading automatically:

#### Manual Registration
Add to `config/app.php`:
```php
'providers' => [
    LaravelSubscriptionModal\LaravelSubscriptionModalServiceProvider::class,
],
```

#### Check Composer Autoload
```bash
composer dump-autoload
```

### 3. FilamentPHP Integration Issues

#### Check Filament Version
```bash
composer show filament/filament
```

#### Verify Layout Integration
Make sure your Filament layout includes the component:
```php
// resources/views/vendor/filament/components/layouts/app.blade.php
<x-filament-panels::layout.base :livewire="$this">
    @livewire('subscription-modal::subscription-badge')
    {{ $slot }}
</x-filament-panels::layout.base>
```

### 4. Debug Mode

Enable debug mode to see detailed error messages:
```env
APP_DEBUG=true
SUBSCRIPTION_DEBUG=true
```

### 5. Check Logs

View Laravel logs for detailed error information:
```bash
tail -f storage/logs/laravel.log
```

### 6. Verify Configuration

Check if the configuration is loaded:
```bash
php artisan config:show subscription-modal
```

### 7. Test Component Registration

Run the registration command to see what's happening:
```bash
php artisan subscription-modal:register-components --verbose
```

### 8. Manual Component Test

Create a simple test route to verify the component works:
```php
// routes/web.php
Route::get('/test-subscription', function () {
    return view('test-subscription');
});
```

```php
<!-- resources/views/test-subscription.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Test Subscription</title>
    @livewireStyles
</head>
<body>
    <h1>Testing Subscription Component</h1>
    @livewire('subscription-modal::subscription-badge')
    @livewireScripts
</body>
</html>
```

### 9. Common Solutions by Environment

#### Laravel 10 + Livewire 3
- Make sure Livewire 3 is properly installed
- Check that the service provider is registered
- Clear all caches

#### Laravel 11 + Livewire 3
- Same as Laravel 10
- Verify compatibility with Laravel 11

#### FilamentPHP 3
- Ensure proper layout integration
- Check z-index conflicts in CSS
- Verify Filament's Livewire integration

### 10. Method dispatch does not exist

If you get the error `Method LaravelSubscriptionModal\Livewire\SubscriptionBadge::dispatch does not exist`:

#### Check Livewire Version
```bash
composer show livewire/livewire
```

#### Solutions:
1. **Livewire 2**: The package now uses `emit()` instead of `dispatch()` for Livewire 2
   - Clear all caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   php artisan optimize:clear
   ```

2. **Livewire 3**: Update to Livewire 3 if you want to use `dispatch()`
   ```bash
   composer require livewire/livewire:^3.6.4
   ```

3. **Clear Livewire Cache**
   ```bash
   php artisan livewire:discover
   php artisan view:clear
   ```

4. **Verify Component Registration**
   ```bash
   php artisan subscription-modal:register-components
   ```

5. **Check Component Syntax**: The package automatically detects Livewire version and uses the correct syntax:
   - **Livewire 2**: Uses `$this->emit()` and `protected $listeners`
   - **Livewire 3**: Uses `$this->dispatch()` and `#[On]` attributes

### 11. Undefined array key errors

If you get errors like `Undefined array key 1` or similar array access errors:

#### Debug the API Response
Visit the debug endpoint to see the actual API response structure:
```
http://your-app.com/subscription-debug
```

#### Check API Response Format
Make sure your API returns the expected JSON structure:
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
        "license_type": "1 sucursal"
      }
    }
  ]
}
```

#### Common Issues:
1. **Empty license_status array**: The API returns an empty array
2. **Wrong data structure**: The API response doesn't match the expected format
3. **String instead of object**: `license_status` is a string like "No active subscription found"
4. **Missing keys**: Required keys like `dias` are missing

#### Solutions:
1. **Check API logs** for the actual response
2. **Verify API endpoint** is correct in `.env`
3. **Test API directly** with curl or Postman
4. **Clear cache** to get fresh data:
   ```bash
   php artisan cache:clear
   ```

### 12. Still Having Issues?

If none of the above solutions work:

1. Check the Laravel version: `php artisan --version`
2. Check the Livewire version: `composer show livewire/livewire`
3. Check the Filament version: `composer show filament/filament`
4. Share the error logs and versions for further assistance

### 13. Emergency Fallback

If you need the component to work immediately, you can manually register it in your `AppServiceProvider`:

```php
// app/Providers/AppServiceProvider.php
public function boot()
{
    if (class_exists(\Livewire\Livewire::class)) {
        \Livewire\Livewire::component('subscription-modal::subscription-badge', \LaravelSubscriptionModal\Livewire\SubscriptionBadge::class);
        \Livewire\Livewire::component('subscription-modal::subscription-modal', \LaravelSubscriptionModal\Livewire\SubscriptionModal::class);
        \Livewire\Livewire::component('subscription-modal::subscription-overlay', \LaravelSubscriptionModal\Livewire\SubscriptionOverlay::class);
    }
}
``` 