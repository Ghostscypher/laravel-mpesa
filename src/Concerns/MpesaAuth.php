<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaAuthException;

trait MpesaAuth
{
    use MpesaGlobalConfig;
    
    protected string $token;
    protected int $expires_at;

    /**
     * Gets the base64 encoded credential generated from consumer key and secret.
     *
     * @return string base64 encoded credentials
     */
    protected function getCredentials(): string
    {
        return base64_encode(sprintf('%s:%s', config('mpesa.consumer_key'), config('mpesa.consumer_secret')));
    }

    /**
     * Generate a token to be used to authenticate requests to the Safaricom API, and store it in the database
     * if there was an error generating the token, an exception will be thrown
     *
     * @param bool $force If true, force the generation of a new token
     * @return string|null
     *
     * @throws MpesaAuthException
     */
    public function generateToken(bool $force = false): ?string
    {
        // Add trace id to the request header
        $this->addRequestTraceId();
        
        // This method will be used to generate a token that will be used to authenticate requests to the Safaricom API
        // If stored in the database, check if the token is still valid
        $token = app(config('mpesa.models.token'))->latest()->first();
        
        if($token && ! $force) {
            $this->token = $token->token;

            return $token->token;
        }

        // Validate that the client has set the consumer key and secret
        if (! config('mpesa.consumer_key') || ! config('mpesa.consumer_secret')) {
            throw new MpesaAuthException('Consumer key and secret are required to generate a token', -1);
        }

        // If the token is not valid, generate a new one
        $response = $this->http_client
            ->replaceHeaders([
                'Authorization' => 'Basic ' . $this->getCredentials(),
            ])
            ->get('/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        // Store the token in the database
        $token = $response->json();

        // If response is not successful, throw an exception
        if (! $response->successful()) {
            throw new MpesaAuthException($response->body(), $response->status());
        }

        $token = app(config('mpesa.models.token'))
            ->create([
                'token' => $token['access_token'],
                'expires_at' => now()->addSeconds($token['expires_in'] - 60),
            ]);

        // Set the token
        $this->token = $token->token;

        return $this->token;
    }

    /**
     * Check if the current token is expired
     *
     * @return bool
     */
    public function isTokenExpired(): bool
    {
        return app(config('mpesa.models.token'))->latest()->first() == null;
    }
}
