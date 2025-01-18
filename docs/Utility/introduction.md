---
title: Mpesa Utility APIs
weight: 1
---

## What are Utility APIs?

These are APIs that are used to perform various utility functions such as:

- Check balance
- Reverse transactions
- Query transaction status

The following .env variables are required in order to authenticate with the Mpesa API:

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENVIRONMENT=
MPESA_SHORTCODE=
MPESA_INITIATOR_NAME=
MPESA_INITIATOR_PASSWORD=
```

## Check balance

This method is used to check the balance of a specific M-Pesa account.

The method takes the following parameters:

- `result_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to. This URL should be accessible by the Safaricom API.
- `queue_timeout_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to if the transaction times out. This URL should be accessible by the Safaricom API.
- `shortcode` *(optional)*: This is the shortcode that you want to use for the transaction. If not provided, the default shortcode will be used. This provides us with flexibility in case you want to use a different shortcode for this request.
- `identifier_type` *(optional)*: This is the type of identifier that you want to use. The default is `4`. If checking for till number balance, you can use `2`.
- `remarks` *(optional)*: This is the remarks of the transaction. This is used to describe the transaction. i.e. the product name, service name, etc. This appears in the admin portal.
- `occassion` *(optional)*: This is the occassion of the transaction. This is used to describe the reason for the transaction. This appears in the admin portal.
- `initiator_name` *(optional)*: This is the name of the initiator. This the username you use to log into the admin portal, or will be the name of user created by the admin. This is used to identify the initiator of the transaction. If not provided, the default initiator name will be used. Please add a valid initiator name in the admin portal.
- `initiator_password` *(optional)*: This is the password of the initiator. This is the password you use to log into the admin portal, or will be the password of user created by the admin. This is used to authenticate the initiator of the transaction. If not provided, the default initiator password will be used. Please add a valid initiator password in the admin portal.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::checkBalance();

// Covert to JSON
if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Reverse transaction

This method is used to reverse a transaction. It requires that it is enabled on your account by the Safaricom team/admin.

The method takes the following parameters:

- `transaction_id`: This is the transaction ID of the transaction you want to reverse.
- `amount`: This is the amount of the transaction you want to reverse.
- `receiver_party` *(optional)*: This is the party that received the transaction. This is the phone number of the person who received the transaction.
- `receiver_identifier_type` *(optional)*: This is the type of identifier that you want to use. The default is `4`. If checking for till number balance, you can use `2`.
- `remarks` *(optional)*: This is the remarks of the transaction. This is used to describe the transaction. i.e. the product name, service name, etc. This appears in the admin portal.
- `occassion` *(optional)*: This is the occassion of the transaction. This is used to describe the reason for the transaction. This appears in the admin portal.
- `queue_timeout_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to if the transaction times out. This URL should be accessible by the Safaricom API.
- `result_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to. This URL should be accessible by the Safaricom API.
- `initiator_name` *(optional)*: This is the name of the initiator. This the username you use to log into the admin portal, or will be the name of user created by the admin. This is used to identify the initiator of the transaction. If not provided, the default initiator name will be used. Please add a valid initiator name in the admin portal.
- `initiator_password` *(optional)*: This is the password of the initiator. This is the password you use to log into the admin portal, or will be the password of user created by the admin. This is used to authenticate the initiator of the transaction. If not provided, the default initiator password will be used. Please add a valid initiator password in the admin portal.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::reverseTransaction('LKXXXX1234', '1000');

// Covert to JSON
if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Query transaction status

This method is used to query the status of a transaction.

The method takes the following parameters:

- `transaction_id`: This is the transaction ID of the transaction you want to query.
- `identifier_type` *(optional)*: This is the type of identifier that you want to use. The default is `4`. If checking for till number balance, you can use `2`.
- `result_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to. This URL should be accessible by the Safaricom API.
- `queue_timeout_url` *(optional)*: This is the URL that Safaricom will send the result of the transaction to if the transaction times out. This URL should be accessible by the Safaricom API.
- `shortcode` *(optional)*: This is the shortcode that you want to use for the transaction. If not provided, the default shortcode will be used. This provides us with flexibility in case you want to use a different shortcode for this request.
- `remarks` *(optional)*: This is the remarks of the transaction. This is used to describe the transaction. i.e. the product name, service name, etc. This appears in the admin portal.
- `occassion` *(optional)*: This is the occassion of the transaction. This is used to describe the reason for the transaction. This appears in the admin portal.
- `initiator_name` *(optional)*: This is the name of the initiator. This the username you use to log into the admin portal, or will be the name of user created by the admin. This is used to identify the initiator of the transaction. If not provided, the default initiator name will be used. Please add a valid initiator name in the admin portal.
- `initiator_password` *(optional)*: This is the password of the initiator. This is the password you use to log into the admin portal, or will be the password of user created by the admin. This is used to authenticate the initiator of the transaction. If not provided, the default initiator password will be used. Please add a valid initiator password in the admin portal.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::queryTransaction('LKXXXX1234');

// Covert to JSON
if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
