---
title: Mpesa C2B APIS (STK Push)
weight: 2
---

## STK Push or Lipa na Mpesa Online or Mpesa Express

STK Push (Sim Tool Kit Push) is a service that allows businesses to receive payments from customers using their mobile phones. The customer initiates the payment process by sending a payment request to the business. The business then processes the payment and sends a confirmation message to the customer.

Ensure that you have the following environment variables set in your `.env` file in order to be able to authenticate with the Mpesa API.

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENVIRONMENT=
MPESA_SHORTCODE=
MPESA_PASSKEY=
```

## STK Push

This is used to initiate an STK push request to the customer's phone number.

The following parameters are supported:

- `phone_number` : The phone number of the customer. The number is automatically formatted to the correct format. So it means you can pass the phone number in following formats. i.e. `254712345678`, `+254712345678`, `0712345678`
- `amount` : The amount to be paid by the customer.
- `account_reference` *(optional)*: The account reference. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `callback_url` *(optional)*: The URL to which the Mpesa API will send the callback response. This is where you will handle the response from the Mpesa API. If not provided, the default callback URL will be used.
- `description` *(optional)*: The description of the transaction. This is used to describe the transaction. i.e. the product name, service name, etc. This appears in the admin portal.
- `transaction_type` *(optional)*: The transaction type. This is used to specify the type of transaction. i.e. CustomerPayBillOnline, CustomerBuyGoodsOnline, etc. CustomerPayBillOnline is the default transaction type and is used by paybills. CustomerBuyGoodsOnline is used by buy goods/till numbers.
- `shortcode` *(optional)*: The shortcode to be used for the transaction. If not provided, the default shortcode will be used.
- `business_short_code` *(optional)*: The business shortcode to be used for the transaction. If not provided, the default business shortcode will be used. This is the same as the shortcode, but is provided here if you ever want to use a different shortcode.
- `party_a` *(optional)*: The party A shortcode. This is the shortcode that will be used to initiate the transaction. If not provided, the default shortcode will be used. This is also the same as the shortcode, but is provided here if you ever want to use a different shortcode. This has some interesting use cases. For example, if you want a different shortcode to be credited with the amount, you can provide the shortcode here.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::stkPush('254712345678', '1', 'Test');

if($response->successfull()) {
    // Success
    $response = $response->json();
    // $response = ['MerchantRequestID' => '12345', 'CheckoutRequestID' => '12345', 'ResponseCode' => '0', 'ResponseDescription' => 'Success. Request accepted for processing', 'CustomerMessage' => 'Success. Request accepted for processing']
} else {
    // Error
    $response = $response->json();
    // $response = ['errorCode' => '400.001.02', 'errorMessage' => 'Invalid Access Token']
}
```

## STK Push Query

This is used to query the status of an STK push request.

The following parameters are supported:

- `checkout_request_id` : The checkout request ID. This is the ID that is returned when you initiate an STK push request.
- `shortcode` *(optional)*: The shortcode to be used for the transaction. If not provided, the default shortcode will be used.

```php

use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::stkPushQuery('wco_123456789');

if($response->successfull()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
