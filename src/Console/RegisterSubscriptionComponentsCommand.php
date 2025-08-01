<?php

namespace LaravelSubscriptionModal\Console;

use Illuminate\Console\Command;
use Livewire\Livewire;
use LaravelSubscriptionModal\Livewire\SubscriptionComponent;

class RegisterSubscriptionComponentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription-modal:register-components';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register Livewire components for the subscription modal package';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Registering subscription modal components...');

        if (!class_exists(\Livewire\Livewire::class)) {
            $this->error('Livewire is not installed!');
            return 1;
        }

        $components = [
            'subscription-modal::subscription-component' => SubscriptionComponent::class,
            'subscription-component' => SubscriptionComponent::class,
        ];

        $registered = 0;
        $failed = 0;

        foreach ($components as $name => $class) {
            try {
                Livewire::component($name, $class);
                $this->line("✓ Registered: {$name}");
                $registered++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to register {$name}: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Registration complete: {$registered} components registered, {$failed} failed.");

        if ($failed > 0) {
            $this->warn('Some components failed to register. Check the error messages above.');
            return 1;
        }

        $this->info('All components registered successfully!');
        return 0;
    }
} 