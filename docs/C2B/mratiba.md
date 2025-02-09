---
title: Mpesa C2B API (Mpesa Ratiba)
weight: 4
---

## Mpesa Ratiba

The Mpesa Ratiba API allows you to create standing orders for your customers. You can use this API to create standing orders for your customers to pay you on a regular basis. This is useful when you want to receive payments from your customers on a regular basis.

The following .env variables are required in order to authenticate with the Mpesa API:

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENV=
MPESA_SHORTCODE=
```

## Create a Standing Order

This allows you to create a standing order for your customers. See [Daraja API Site](https://developer.safaricom.co.ke/APIs/MpesaRatiba)
The method takes the following parameters:

- `standing_order_name`: The name of the standing order. This is used to identify the standing order. This must be unique.
- `phone_number`: The phone number of the customer. The number is automatically formatted to the correct format. So it means you can pass the phone number in following formats. i.e. `254712345678`, `+254712345678`, `0712345678`
- `amount`: The amount to be paid by the customer.
- `start_date`: The start date of the standing order. This is the date when the standing order will start. The date must be in the format `YYYYMMDD`. Example: `20221201`
- `end_date`: The end date of the standing order. This is the date when the standing order will end. The date must be in the format `YYYYMMDD`. Example: `20231201`
- `frequency`: The frequency of the standing order. This is the frequency at which the standing order will be executed
  - 1: One Off/Once
  - 2: Daily
  - 3: Weekly
  - 4: Monthly
  - 5: Bi-Monthly
  - 6: Quarterly
  - 7: Half Year
  - 8: Yearly
- `account_reference`: The account reference. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `callback_url` *(optional)*: The URL to which the Mpesa API will send the callback response. This is where you will handle the response from the Mpesa API. If not provided, the default callback URL will be used.
- `description` *(optional)*: The description. This is a description of the standing order.
- `business_short_code` *(optional)*: The business short code. This is the short code of the business that will receive the funds. If not provided, the default short code will be used.
- `transaction_type` *(optional)*: The transaction type. This is used to specify the type of transaction. i.e. Standing Order Customer Pay Bill, Standing Order Customer Buy Goods, etc.
  - Standing Order Customer Pay Bill - is the default transaction type and is used by paybills.
  - Standing Order Customer Pay Marchant - is used by buy goods/till numbers.
- `reciever_identifier_type` *(optional)*: The receiver identifier type. This is used to specify the type of identifier. i.e. 4 we don't recommend changing this value.
- `shortcode` *(optional)*: The shortcode to be used for the transaction. If not provided, the default shortcode will be used.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::ratiba('Lipa Mdogo Mdogo', '254712345678', '1', '20221201', '20231201', '3', 'July Payment');

if($response->getStatusCode() == 200) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
