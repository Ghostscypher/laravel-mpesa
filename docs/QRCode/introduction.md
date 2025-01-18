---
title: Mpesa QRCode API
weight: 1
---

## QRCode API

The QRCode API enables you to generate QR codes for payments. The client can scan directly from Mpesa App to finish the payment.

The following .env variables are required in order to authenticate with the Mpesa API:

```dotenv
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_ENVIRONMENT=
MPESA_SHORTCODE=
```

## Generate QR Code

This allows you to generate a QR code for payments. The method takes the following parameters:

- `merchant_name`: The name of the merchant.
- `ref_no`: The reference number. This is used to identify the transaction. It is usually a unique identifier for the transaction. i.e. the account number, order number, etc.
- `amount`: The amount to be paid.
- `CPI` *(optional)*: The Consumer Paybill Number. This is the paybill number of the consumer. If not provided, the default paybill number will be used.
- `trx_code` *(optional)*: The transaction code. This is used to specify the type of transaction. i.e. PB for Paybill, MP for Mpesa, etc.
  - BG: Buy Goods
  - WA: Withdrawal at Agent till
  - PB: Paybill
  - SM: Send Money (Mobile number)
  - SB: Sent to Business. Business number CPI in MSISDN format.
- `size` *(optional)*: The size of the QR code. This is the size of the QR code in pixels. The default size is 300 pixels.

```php
use \Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::generateQRCode('naivas', 'Test','1');

// TODO: Handle the response

```
