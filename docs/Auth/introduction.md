---
title: Initiating Mpesa API
weight: 1
---

## MpesaAuth Trait

We assume that you have already installed the package and have the necessary credentials from Safaricom. If you don't have the credentials, you can get them by registering on the [Safaricom Developer Portal](https://developer.safaricom.co.ke/).

The MpesaAuth trait contains several methods that will help you get started with the Mpesa APIs.

Ensure that you have the following environment variables set in your `.env` file in order to be able to authenticate with the Mpesa API.

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENVIRONMENT=
MPESA_SHORTCODE=
MPESA_PASSKEY=
MPESA_INITIATOR_NAME=
MPESA_INITIATOR_PASSWORD=
```

## Mpesa::generateToken(): string

Do note that this is automatically called when you make a request to any of the Mpesa APIs. You can also manually generate the token by calling the `generateToken` method.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 *  @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 */
$token = Mpesa::generateToken();

// Or to force a new token to be generated
/**
 *  @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 */
$token = Mpesa::generateToken(true);

// $token = 1234567890.....

// To use the token in your requests you will need to set it in the headers
$headers = [
    'Authorization' => 'Bearer ' . $token,
    'Content-Type' => 'application/json',
];
```

The method will internally make an API request to the Safaricom API and return the token. The token is then stored in the database if we have set the `MPESA_FEATURE_STORE_TOKEN` environment variable to true. If not, the token is returned as a string.

## Mpesa::isTokenExpired(): bool

This method will check if the token has expired. If the token has expired, it will return `true`, otherwise `false`.

**Warning**: This method will only work if the `MPESA_FEATURE_STORE_TOKEN` environment variable is set to true. It checks the token expiry date in the database.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

if (Mpesa::isTokenExpired()) {
    // Token has expired
}
```

## Mpesa::generateSecurityCredential(string $initiatorPassword): string

This method will generate the security credential, it is used in some of the Mpesa APIs.
If the `$initiatorPassword` is not provided, it will use the `MPESA_INITIATOR_PASSWORD` environment variable.

**Note**: the credentials are generated based on the environment, if in production the credentials will be generated using the production credentials, and if in sandbox the credentials will be generated using the sandbox credentials.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
* @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 */
$securityCredential = Mpesa::generateSecurityCredential("myInitiatorPassword");

// $securityCredential = ejfedsc...

```
