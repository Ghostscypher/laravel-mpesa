---
title: Mpesa C2B API (Register callback URLs)
weight: 3
---

## Register URLs

To receive callbacks from Safaricom, you need to register the URLs that Safaricom will send the confirmation/validation callbacks to.

## Register confirmation and validation URLs

To register the URLs, you need to call the `registerUrls` method on the `Mpesa` facade.

This method takes the following parameters:

- `confirmationUrl` *(optional)*: This is the URL that Safaricom will send a confirmation request to. This URL should be accessible by the Safaricom API.
- `validationUrl` *(optional)*: This is the URL that Safaricom will send a validation request to. This URL should be accessible by the Safaricom API. Validation is used to confirm the details of the transaction before it is processed. This is useful when you want to confirm something like the account number or the amount before processing the transaction.
- `responseType` *(optional)*: This is the response type that you want to receive from Safaricom. The default is `Completed`. This means that you will receive a response when the transaction is completed. You can also use `Canceled` to receive a response when the transaction is cancelled.
- `shortcode` *(optional)*: This is the shortcode that you want to use for the transaction. If not provided, the default shortcode will be used. This provides us with flexibility in case you want to use a different shortcode for this request.

**Note**:

- The URLs should be publicly accessible.
- In order to enable validation, you will need to contact Safaricom to enable it for you.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::registerUrls('https://example.com/confirmation', 'https://example.com/validation', 'Completed', '10000');

// Covert to JSON
if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
