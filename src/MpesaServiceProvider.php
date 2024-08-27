<?php

namespace Ghostscypher\Mpesa;

use Ghostscypher\Mpesa\Commands\RegisterC2BCallbackCommand;
use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MpesaServiceProvider extends PackageServiceProvider
{
    public const CONTROLLERS = __DIR__.'/../stubs/controllers';

    protected function shortName(): string
    {
        return 'mpesa';
    }

    protected function publishController(): void
    {
        // Check if the controllers directory exists, if not, create it
        if (! is_dir(app()->path('Http/Controllers/LaravelMpesa'))) {
            mkdir(app()->path('Http/Controllers/LaravelMpesa'), 0755, true);
        }

        // Foreach file in the stubs/controllers directory, copy it to the Http/Controllers/LaravelMpesa directory
        $files = glob(static::CONTROLLERS.'/*.php');

        foreach ($files as $file) {
            $file_name = basename($file);

            // Check if the file already exists, if it does, skip it
            if (file_exists(app()->path('Http/Controllers/LaravelMpesa/'.$file_name))) {
                continue;
            }

            copy($file, app()->path('Http/Controllers/LaravelMpesa/'.$file_name));
        }
    }

    public function bootingPackage()
    {
        // Controllers
        $this->publishes([static::CONTROLLERS => app()->path('Http/Controllers/LaravelMpesa')], 'controllers');
    }

    public function configurePackage(Package $package): void
    {
        $migrations = glob(__DIR__.'/../database/migrations/*.php.stub');
        $migrations = array_map(function ($migration_name) {
            return str_replace('.php.stub', '', basename($migration_name));
        }, $migrations);

        $package
            ->name('mpesa')
            ->hasConfigFile('mpesa')
            ->hasMigrations($migrations)
            ->hasCommand(RegisterC2BCallbackCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->publish('controllers')
                    ->askToRunMigrations();

                // Publish the controllers
                $this->publishController();

                //->askToStarRepoOnGitHub('Ghostscypher/laravel-mpesa'); // Will probably be removed in future versions
            });
    }

    public function packageRegistered()
    {
        // Merge the package config file
        $this->mergeConfigFrom(__DIR__.'/../config/mpesa.php', 'mpesa');

        // If registration of routes is enabled, register the routes
        if (config('mpesa.features.register_routes')) {
            $this->registerRoutes();
        }

        // Register the Mpesa facade
        $this->app->bind('mpesa', function () {
            return new Mpesa;
        });
    }

    protected function registerRoutes()
    {
        $default_routes = [
            'stk_push_callback_url' => '/lmp/stk/push/callback',
            'c2b_validation_url' => '/lmp/c2b/validation',
            'c2b_confirmation_url' => '/lmp/c2b/confirmation',
            'b2c_result_url' => '/lmp/b2c/result',
            'b2c_timeout_url' => '/lmp/b2c/timeout',
            'b2b_result_url' => '/lmp/b2b/result',
            'b2b_timeout_url' => '/lmp/b2b/timeout',
            'b2b_stk_callback_url' => '/lmp/b2b/stk/callback',
            'status_result_url' => '/lmp/status/result',
            'status_timeout_url' => '/lmp/status/timeout',
            'reversal_result_url' => '/lmp/reversal/result',
            'reversal_timeout_url' => '/lmp/reversal/timeout',
            'balance_result_url' => '/lmp/balance/result',
            'balance_timeout_url' => '/lmp/balance/timeout',
            'bill_manager_callback_url' => '/lmp/bill/manager/callback',
        ];

        // If the route is not set in the config file, use the default route
        foreach ($default_routes as $key => $route) {
            if (empty(config('mpesa.'.$key))) {
                // Url + route
                config(['mpesa.'.$key => $route]);
            }
        }

        // Register the routes
        $this->loadRoutesFrom(__DIR__.'/../routes/mpesa_callback.php');
    }

    /**
     * @return void
     */
    public function packageBooted()
    {
        // Register events
        if (config('mpesa.features.enable_logging')) {
            Event::listen(RequestSending::class, config('mpesa.listeners.log_request'));
            Event::listen(ResponseReceived::class, config('mpesa.listeners.log_response'));
        }
    }
}
