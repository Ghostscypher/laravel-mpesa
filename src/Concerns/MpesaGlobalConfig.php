<?php

namespace Ghostscypher\Mpesa\Concerns;

use Illuminate\Contracts\Validation\Factory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/**
 * Trait MpesaGlobalConfig
 *
 * Internal class for handling global configurations and utility functions
 */
trait MpesaGlobalConfig
{
    // Properties
    public string $environment;

    public string $base_url;

    // Internal properties
    protected PendingRequest $http_client;

    public function __construct()
    {
        $this->refreshEnv();
    }

    // Update the HTTP client with the required headers
    protected function updateHttpClient(array $headers = [], array $options = []): PendingRequest
    {
        // Merge the headers
        $headers = array_merge(
            $headers,
            [
                // Add the request ID and timestamp, this is for tracking requests
                'X-Ghostscypher-Laravel-Mpesa-Request-ID' => uniqid(microtime(true), true),
                'X-Ghostscypher-Laravel-Mpesa-Request-Timestamp' => now()->format('Y-m-d H:i:s'),
            ]
        );

        // Merge data
        $options = array_merge(
            $options,
            [
                'headers' => $headers,
            ]
        );

        $this->http_client->withOptions($options);

        return $this->http_client;
    }

    /**
     * Refresh the environment variables, this prevents errors between requests
     * during the app lifecycle
     *
     * @return void
     */
    protected function refreshEnv()
    {
        $this->environment = config('mpesa.env');
        $this->base_url = $this->environment === 'production' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        $this->http_client = Http::baseUrl($this->base_url)
            ->withHeaders([
                'Accept' => 'application/json',
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/json',
            ]);

        // Set default timeout for the HTTP client of 3 minutes
        $this->http_client->timeout(180);
    }

    /**
     * Format the phone number to E.164 format, without the plus sign
     *
     * @param  string  $phone_number  The phone number to format
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
     */
    public function isProduction(): bool
    {
        return config('mpesa.env') === 'production';
    }

    /**
     * Check if the current environment is sandbox
     */
    public function isSandbox(): bool
    {
        return ! $this->isProduction();
    }

    /**
     * Create a new Validator instance.
     *
     * @return ($data is null ? \Illuminate\Contracts\Validation\Factory : \Illuminate\Contracts\Validation\Validator)
     */
    protected static function validate(?array $data = null, array $rules = [], array $messages = [], array $attributes = [])
    {
        $factory = app(Factory::class);
        $factory->extend('phone_number', function ($attribute, $value, $parameters, $validator) {
            // Validate that the phone number follows the format 2547XXXXXXXX or 2541XXXXXXXX
            return preg_match('/^2547\d{8}$/', $value) || preg_match('/^2541\d{8}$/', $value);
        });

        $factory->extend('phone_number_lax', function ($attribute, $value, $parameters, $validator) {
            // Validate that the phone number follows the format 2547XXXXXXXX or 2541XXXXXXXX or 07XXXXXXXX or 01XXXXXXXX
            return preg_match('/^2547\d{8}$/', $value) || preg_match('/^2541\d{8}$/', $value) || preg_match('/^07\d{8}$/', $value) || preg_match('/^01\d{8}$/', $value);
        });

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($data ?? [], $rules, $messages, $attributes);
    }

    /**
     * Generate the originator conversation ID which is unique for each transaction
     */
    public function generateOriginatorConversationId($prefix = ''): string
    {
        return uniqid($prefix, true);
    }

    /**
     * Parse mpesa balance string
     *
     * @example Balance Working Account|KES|700000.00|700000.00|0.00|0.00&Float
     *                    Account|KES|0.00|0.00|0.00|0.00&Utility
     *                    Account|KES|228037.00|228037.00|0.00|0.00&Charges Paid
     *                    Account|KES|-1540.00|-1540.00|0.00|0.00&Organization Settlement
     *                    Account|KES|0.00|0.00|0.00|0.00
     */
    public static function parseMpesaBalance(string $balance): array
    {
        $balance = explode('&', $balance);

        $parsed = [];

        foreach ($balance as $item) {
            $item = explode('|', $item);

            $parsed[$item[0]] = [
                'account' => $item[0],
                'currency' => $item[1],
                'available_balance' => $item[2],
                'current_balance' => $item[3],
                'reserved_balance' => $item[4],
                'unclear_balance' => $item[5],
            ];
        }

        return $parsed;
    }

    /**
     * Takes an array of data and deconstructs it into a key-value pair based on the key_name and value_key
     *
     * @example
     * $data = [
     *    ['Name' => 'John Doe', 'Value' => '0700000000'],
     *   ['Name' => 'Jane Doe', 'Value' => '0710000000'],
     * ];
     * deconstructData($data, 'Name', 'Value');
     * Returns ['John Doe' => '0700000000', 'Jane Doe' => '0710000000']
     *
     * @param  array  $data  The data to deconstruct
     * @param  mixed  $key_name  The key to use as the key in the key-value pair
     * @param  mixed  $value_key  The key to use as the value in the key-value pair
     * @param  mixed  $case_insensitive  Whether to make the key case insensitive
     */
    public static function deconstructData($data, $key_name = 'Name', $value_key = 'Value', $case_insensitive = false): \Illuminate\Support\Collection
    {
        $deconstructed = [];

        foreach ($data as $item) {
            if (! isset($item[$key_name])) {
                continue;
            }

            $key = $item[$key_name];
            $value = $item[$value_key] ?? null;

            if ($case_insensitive) {
                $key = strtolower($key);
            }

            $deconstructed[$key] = $value;
        }

        return collect($deconstructed);
    }

    /**
     * Generates full URL for the callback
     *
     * @param  string  $route  The route to generate the URL for
     */
    public static function generateCallbackUrl(string $route): string
    {
        // Check if the route has parameters, if it does get the parameters\
        $parameters = explode('?', $route);
        $route = array_shift($parameters);

        // Extract the parameters
        if (count($parameters) > 0) {
            $parameters = explode('&', $parameters[0] ?? '');
            $parameters = collect($parameters)->mapWithKeys(function ($parameter) {
                $parameter = explode('=', $parameter);

                return [$parameter[0] => $parameter[1] ?? ''];
            })->toArray();
        }

        // Check if the route is a named route, if it is, generate the URL
        if (Route::has($route)) {
            return route($route, $parameters);
        }

        // Get the base URL
        $base_url = url($route);
        $base_url = rtrim($base_url, '/');
        $base_url = rtrim($base_url, '?');

        // If has a query string, add it
        if (count($parameters) > 0) {
            $base_url .= '?'.http_build_query($parameters, '', '&');
        }

        // Generate the full URL
        return $base_url;
    }
}
