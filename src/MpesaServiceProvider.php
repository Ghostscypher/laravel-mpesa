<?php

namespace Ghostscypher\Mpesa;

use Illuminate\Http\Client\Events\RequestSending;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Support\Facades\Event;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MpesaServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $migrations = glob(__DIR__ . '/../database/migrations/*.php.stub');
        $migrations = array_map('basename', $migrations);

        $package
            ->name('mpesa')
            ->hasConfigFile('mpesa')
            ->hasMigrations($migrations)
            ->hasCommand(InstallCommand::class);
    }

    public function packageRegistered()
    {
        $this->app->bind('mpesa', function () {
            return new Mpesa();
        });
    }

    /**
     *
     * @return void
     */
    public function packageBooted()
    {
        // Register events
        if(config('mpesa.features.enable_logging')) {
            Event::listen(RequestSending::class, config('mpesa.listeners.log_request'));
            Event::listen(ResponseReceived::class, config('mpesa.listeners.log_response'));
        }
    }
}
