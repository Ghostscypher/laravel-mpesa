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
     * @param  bool  $force  If true, force the generation of a new token
     *
     * @throws MpesaAuthException
     */
    public function generateToken(bool $force = false): ?string
    {
        // Init env
        $this->refreshEnv();

        // Flag to store in the database or force token generation
        $force = (! config('mpesa.features.store_tokens')) || $force;

        // If not forced and token exists in the database, return it
        if (! $force && ($token = app(config('mpesa.models.token'))->latest()->first())) {
            $this->token = $token->token;

            $this->updateHttpClient([
                'Authorization' => 'Bearer '.$this->token,
            ]);

            return $token->token;
        }

        // Validate that the client has set the consumer key and secret
        if (! config('mpesa.consumer_key') || ! config('mpesa.consumer_secret')) {
            throw new MpesaAuthException('Consumer key and secret are required to generate a token', -1);
        }

        // If the token is not valid, generate a new one
        $this->updateHttpClient([
            'Authorization' => 'Basic '.$this->getCredentials(),
        ]);

        $response = $this->http_client
            ->get('/oauth/v1/generate', [
                'grant_type' => 'client_credentials',
            ]);

        // Store the token in the database
        $token = $response->json();

        // If response is not successful, throw an exception
        if (! $response->successful()) {
            throw new MpesaAuthException($response->body(), $response->status());
        }

        // If access token is not set, throw an exception
        if (! isset($token['access_token'])) {
            throw new MpesaAuthException('Access token not set in response: '.$response->body(), $response->status());
        }

        // If expires in is not set, default to 3600
        if (! isset($token['expires_in'])) {
            $token['expires_in'] = 3600;
        }

        // Store the token in the database
        if (config('mpesa.features.store_tokens')) {
            // Store the token in the database
            $token = app(config('mpesa.models.token'))
                ->updateOrCreate([
                    'token' => $token['access_token'],
                ], [
                    'expires_at' => now()->addSeconds($token['expires_in'] - 60),
                ]);
        }

        // Set the token
        $this->token = $token->token;

        // Update the headers
        $this->updateHttpClient([
            'Authorization' => 'Bearer '.$this->token,
        ]);

        return $this->token;
    }

    /**
     * Check if the current token is expired
     */
    public function isTokenExpired(): bool
    {
        return app(config('mpesa.models.token'))->latest()->first() == null;
    }

    /**
     * Generate the security credential to be used in the request
     *
     * @param  string|null  $initiator_password  The password to be used to generate the security credential
     *
     * @throws MpesaAuthException
     */
    public function generateSecurityCredential($initiator_password = null): string
    {
        // Get the path to correct cert
        $cert_path = realpath(
            $this->isProduction() ?
            __DIR__.'/../certificates/production.cer' :
            __DIR__.'/../certificates/sandbox.cer'
        );

        $encrypted = '';

        // Get the contents of the
        $cert_content = file_get_contents($cert_path);

        // Create the public key
        $public_key = openssl_pkey_get_public($cert_content);

        if (! openssl_public_encrypt(
            $initiator_password ?? config('mpesa.initiator_password'),
            $encrypted,
            $public_key,
            OPENSSL_PKCS1_PADDING
        )
        ) {
            throw new MpesaAuthException(
                'Unable to generate security credential. Perhaps it is bigger than the key size?'
            );
        }

        return base64_encode($encrypted);
    }
}
