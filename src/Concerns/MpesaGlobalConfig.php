<?php

namespace Ghostscypher\Mpesa\Concerns;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

trait MpesaGlobalConfig
{
    // Properties
    public string $environment;
    public string $base_url;
    public string $short_code;
    public string $consumer_key;
    public string $consumer_secret;
    public string $passkey;
    public string $initiator_name;
    public string $initiator_password;
    public string $b2c_shortcode;

    // Models
    public string $token_model;

    // Internal properties
    protected PendingRequest $http_client;

    public function __construct()
    {
        $this->environment = config('mpesa.env');
        $this->base_url = $this->environment === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';
        $this->consumer_key = config('mpesa.consumer_key');
        $this->consumer_secret = config('mpesa.consumer_secret');
        $this->passkey = config('mpesa.passkey');
        $this->short_code = config('mpesa.shortcode');
        $this->initiator_name = config('mpesa.initiator_name');
        $this->initiator_password = config('mpesa.initiator_password');
        $this->b2c_shortcode = config('mpesa.b2c_shortcode');

        $this->http_client = Http::baseUrl($this->base_url)
            ->withHeaders([
                "Accept" => "application/json",
                "Cache-Control" => "no-cache",
                "Content-Type" => "application/json",
            ]);
    }

    protected function addRequestTraceId()
    {
        $this->http_client->replaceHeaders([
            'X-Ghostscypher-Laravel-Mpesa-Request-ID' => uniqid(microtime(true), true),
            'X-Ghostscypher-Laravel-Mpesa-Request-Timestamp' => now()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Format the phone number to E.164 format, without the plus sign
     *
     * @param string $phone_number The phone number to format
     * @return string
     *
     * @see https://github.com/Iankumu/mpesa/blob/cd9dd65c667faca8d436c8a1eafe0ae089b4fd22/src/Utils/MpesaHelper.php#L61
     */
    public static function formatPhoneNumberE164($phone_number): string
    {
        // Some validations for the phonenumber to format it to the required format
        $phone_number = (substr($phone_number, 0, 1) == '+') ? str_replace('+', '', $phone_number) : $phone_number;
        $phone_number = (substr($phone_number, 0, 1) == '0') ? preg_replace('/^0/', '254', $phone_number) : $phone_number;
        $phone_number = (substr($phone_number, 0, 1) == '7') ? "254{$phone_number}" : $phone_number;

        return $phone_number;
    }

    /**
     * Check if the current environment is production
     * @return bool
     */
    public function isProduction(): bool
    {
        return config('mpesa.env') === 'production';
    }

    /**
     * Check if the current environment is sandbox
     * @return bool
     */
    public function isSandbox(): bool
    {
        return ! $this->isProduction();
    }

    /**
     * Create a new Validator instance.
     *
     * @param  array|null  $data
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $attributes
     * @return ($data is null ? \Illuminate\Contracts\Validation\Factory : \Illuminate\Contracts\Validation\Validator)
     */
    protected static function validate(?array $data = null, array $rules = [], array $messages = [], array $attributes = [])
    {
        $factory = app(Factory::class);
        $factory->extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            // Validate that the phone number follows the format 2547XXXXXXXX or 2541XXXXXXXX
            return preg_match('/^2547\d{8}$/', $value) || preg_match('/^2541\d{8}$/', $value);
        });

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data ?? [], $rules, $messages, $attributes);
    }
}
