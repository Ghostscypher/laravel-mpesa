<?php

namespace Ghostscypher\Mpesa\Commands;

use Illuminate\Console\Command;

class RegisterC2BCallbackCommand extends Command
{
    protected $signature = 'mpesa:register-c2b-urls {validation_url=} {confirmation_url=} {--response_type=Completed} {--shortcode=}';

    public function handle(): int
    {
        $validation_url = $this->argument('validation_url') ?? config('mpesa.c2b_validation_url');
        $confirmation_url = $this->argument('confirmation_url') ?? config('mpesa.c2b_confirmation_url');
        $response_type = $this->option('response_type');
        $shortcode = $this->option('shortcode') ?? config('mpesa.shortcode');

        $this->info('Registering C2B callback URL...');

        try {
            $response = app('mpesa')->registerUrl($validation_url, $confirmation_url, $response_type, $shortcode);

            if ($response->isSuccessful()) {
                $this->info('C2B callback URL registered successfully!');
                $this->info($response->json());
            } else {
                $this->error('Failed to register C2B callback URL!');
                $this->error($response->json());
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return 1;
        }

        return 0;
    }
}
