<?php

namespace Ghostscypher\Mpesa\Commands;

use Illuminate\Console\Command;

class InstallMpesaPackageCommand extends Command
{
    protected $signature = 'mpesa:install';

    public function handle(): int
    {
        $this->info('Installing Mpesa package...');

        $this->call('vendor:publish', [
            '--provider' => 'Ghostscypher\Mpesa\MpesaServiceProvider',
            '--tag' => 'controllers',
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Ghostscypher\Mpesa\MpesaServiceProvider',
            '--tag' => 'config',
        ]);

        $this->call('vendor:publish', [
            '--provider' => 'Ghostscypher\Mpesa\MpesaServiceProvider',
            '--tag' => 'migrations',
        ]);

        // Ask the user to run the migrations
        if ($this->confirm('Do you want to run the migrations now?')) {
            $this->call('migrate');
        }

        $this->info('Mpesa package installed successfully!');

        return 0;
    }
}
