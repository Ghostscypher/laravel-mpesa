---
title: Mpesa Utility APIs
weight: 1
---

## B2B APIs

The B2B APIs enable Business to Business (B2B) transactions between a business and another business e.g. The transfer of funds between a corporate organization and a service provider etc.

The following .env variables are required in order to authenticate with the Mpesa API:

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENVIRONMENT=
MPESA_SHORTCODE=
MPESA_INITIATOR_NAME=
MPESA_INITIATOR_PASSWORD=
MPESA_PARTNER_NAME=
```

The following actions are available in the B2B trait:

- Initiating a B2B Transaction
- Remit tax to KRA
- Initiate a B2B STK Push

### Initiating a B2B Transaction

This allows you to initiate a B2B transaction between a business and another business.

The method takes the following parameters:

- `receiving_shortcode`: The shortcode of the business that will receive the funds.
- `amount`: The amount to be transferred.
- `account_reference`: The account reference. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `queue_timeout_url` *(optional)*: The URL to which the Mpesa API will send the queue timeout response. This is where you will handle the response from the Mpesa API. If not provided, the default queue timeout URL will be used.
- `result_url` *(optional)*: The URL to which the Mpesa API will send the result response. This is where you will handle the response from the Mpesa API. If not provided, the default result URL will be used.
- `command_id` *(optional)*: The command ID. This is used to specify the type of transaction. i.e. BusinessPayBill, BusinessBuyGoods, BusinessPayToBulk, DisburseFundsToBusiness, BusinessToBusinessTransfer, MerchantToMerchantTransfer. There may be other command IDs not listed here.
  - BusinessPayBill is the default command ID and is used by paybills.
  - BusinessBuyGoods is used by buy goods/till numbers. BusinessPayToBulk is used to pay to a bulk account.
  - DisburseFundsToBusiness is used to disburse funds to a business account.
  - BusinessToBusinessTransfer is used to transfer funds between businesses.
  - MerchantToMerchantTransfer is used to transfer funds between merchants.
- `remarks` *(optional)*: The remarks. This is a description of the transaction.
- `requester_phone_number` *(optional)*: The phone number of the requester. This is the phone number of the person making the request. This is usually the organization's phone number.
- `originator_conversation_id` *(optional)*: The conversation ID. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `shortcode` *(optional)*: The shortcode of the business that will send the funds. If not provided, the default shortcode will be used.
- `initiator_name` *(optional)*: The name of the initiator. This is the name of the person making the request. This is usually the organization's name.
- `initiator_password` *(optional)*: The password of the initiator. This is the password of the person making the request. This is usually the organization's password.
- `sender_identifier_type` *(optional)*: The sender identifier type. This is used to specify the type of identifier. i.e. 4 we don't recommend changing this value.
- `reciever_identifier_type` *(optional)*: The receiver identifier type. This is used to specify the type of identifier. i.e. 4 we don't recommend changing this value.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::B2B('600000', '1', 'Test');

if($response->successfull()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

### Remit tax to KRA

This allows you to remit tax to KRA.

The method takes the following parameters:

- `amount`: The amount to be transferred.
- `account_reference`: The KRA pin number. e.g. P051234567A.
- `queue_timeout_url` *(optional)*: The URL to which the Mpesa API will send the queue timeout response. This is where you will handle the response from the Mpesa API. If not provided, the default queue timeout URL will be used.
- `result_url` *(optional)*: The URL to which the Mpesa API will send the result response. This is where you will handle the response from the Mpesa API. If not provided, the default result URL will be used.
- `remarks` *(optional)*: The remarks. This is a description of the transaction.
- `requester_phone_number` *(optional)*: The phone number of the requester. This is the phone number of the person making the request. This is usually the organization's phone number.
- `originator_conversation_id` *(optional)*: The conversation ID. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `shortcode` *(optional)*: The shortcode of the business that will send the funds. If not provided, the default shortcode will be used.
- `initiator_name` *(optional)*: The name of the initiator. This is the name of the person making the request. This is usually the organization's name.
- `initiator_password` *(optional)*: The password of the initiator. This is the password of the person making the request. This is usually the organization's password.
- `party_b`: The party B shortcode. This is the shortcode that will receive the funds. This is the KRA shortcode i.e. 572572.
- `sender_identifier_type` *(optional)*: The sender identifier type. This is used to specify the type of identifier. i.e. 4 we don't recommend changing this value.
- `reciever_identifier_type` *(optional)*: The receiver identifier type. This is used to specify the type of identifier. i.e. 4 we don't recommend changing this value.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::B2BRemitTax('1', 'Test');

if($response->successfull()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

### Initiate a B2B STK Push

This allows you to initiate a B2B STK push. This is different from the normal STK push. The B2B STK push is used to initiate a payment from a business to another business.

The method takes the following parameters:

- `reciever_shortcode`: The shortcode of the business that will receive the funds.
- `amount`: The amount to be transferred.
- `account_reference`: The account reference. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `callback_url` *(optional)*: The URL to which the Mpesa API will send the callback response. This is where you will handle the response from the Mpesa API. If not provided, the default callback URL will be used.
- `shortcode` *(optional)*: The shortcode of the business that will send the funds. If not provided, the default shortcode will be used.
- `partner_name` *(optional)*: The name of the partner. This is the name of the business that will receive the funds.
- `request_reference_id` *(optional)*: The request reference ID. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::B2BStkPush('600000', '1', 'Test');

if($response->successfull()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

