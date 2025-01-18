---
title: Mpesa B2C APIs
weight: 1
---

## B2C APIs

The M-Pesa B2C API is a powerful and versatile API that enables businesses to disburse funds to both registered and unregistered M-Pesa customers, in real-time. The API is ideal for businesses that need to make payments to individuals, such as refunds, rewards, salaries, and incentives.

This trait contains several methods that will help you get started with the B2C APIs.

## Initiating a B2C Transaction

The `B2C` method is used to initiate a B2C transaction. The method takes in the following parameters:

- `phone_number`: The phone number of the customer. The number is automatically formatted to the correct format. So it means you can pass the phone number in following formats. i.e. `254712345678`, `+254712345678`, `0712345678`
- `amount`: The amount to be paid to the customer.
- `queue_timeout_url` *(optional)*: The URL that Safaricom will send the result of the transaction to if the transaction times out. This URL should be accessible by the Safaricom API.
- `result_url` *(optional)*: The URL that Safaricom will send the result of the transaction to. This URL should be accessible by the Safaricom API.
- `command_id` *(optional)*: The command ID. This is used to specify the type of transaction. The default is `BusinessPayment`. Other options are `SalaryPayment` and `PromotionPayment`. There are many others that you can use. You can check the Safaricom documentation for more information.
- `remarks` *(optional)*: The remarks of the transaction. This is used to describe the transaction. i.e. the product name, service name, etc. This appears in the admin portal.
- `occasion` *(optional)*: The occasion of the transaction. This is used to describe the reason for the transaction. This appears in the admin portal.
- `originator_conversation_id` *(optional)*: The conversation ID of the transaction. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `shortcode` *(optional)*: The shortcode to be used for the transaction. If not provided, the default shortcode will be used.
- `initiator_name` *(optional)*: The name of the initiator. This the username you use to log into the admin portal, or will be the name of user created by the admin. This is used to identify the initiator of the transaction. If not provided, the default initiator name will be used. Please add a valid initiator name in the admin portal.
- `initiator_password` *(optional)*: The password of the initiator. This is the password you use to log into the admin portal, or will be the password of user created by the admin. This is used to authenticate the initiator of the transaction. If not provided, the default initiator password will be used. Please add a valid initiator password in the admin portal.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaRequestException
 */
$response = Mpesa::B2C('254712345678', '1');

if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
