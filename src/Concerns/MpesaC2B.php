<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

trait MpesaC2B
{
    use MpesaAuth;

    /**
     * Summary of registerUrl
     *
     * @param  string  $validdation_url  - The URL to receive validation request from Safaricom, note that validation needs to be enabled for this endpoint to be called
     * @param  string  $confirmation_url  - Once payment is successful this endpoint will be called
     * @param  string  $response_type  - Incase validation endpoint is not reachable we use this to determine whether to complete or cancel the transaction (Canceled, Completed)
     * @param  string  $shortcode  - The short code to be used for the transaction
     */
    public function registerUrl(
        ?string $validdation_url = null,
        ?string $confirmation_url = null,
        string $response_type = 'Completed',
        ?string $shortcode = null
    ): Response {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'validation_url' => $validdation_url ?? self::generateCallbackUrl(config('mpesa.c2b_validation_url')),
            'confirmation_url' => $confirmation_url ?? self::generateCallbackUrl(config('mpesa.c2b_confirmation_url')),
            'response_type' => $response_type,
            'shortcode' => $shortcode ?? config('mpesa.shortcode'),
        ], [
            'validation_url' => 'required|url',
            'confirmation_url' => 'required|url',
            'response_type' => 'required|in:Completed,Canceled',
            'shortcode' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/c2b/register', $validator->validated());
    }
}
