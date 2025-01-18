---
title: Mpesa Bill Manager APIs
weight: 1
---

## Bill Manager APIs

The Bill Manager APIs allows you to have a single point of integration for all your payment needs. You can use the Bill Manager APIs to manage all your payments in one place. See the [Safaricom documentation](https://developer.safaricom.co.ke/APIs/BillManager) for more information.

The bill manager has the following lifecycle:

1. Opt In - This is the process of registering for the service.
2. Create invoices single or bulk - This is the process of creating invoices.
3. Reconcile - This is the process of reconciling the invoices.
4. Cancel Single Invoice - This is the process of cancelling a single invoice.
5. Cancel Bulk Invoices - This is the process of cancelling multiple invoices.

## Opt in

This is used to opt into the bill manager service.

The method takes the following parameters:

- `email`: The email address of the business, this is the email used when registering your business.
- `phone_number`: The phone number of the business, this is the phone number used when registering your business.
- `callback_url` *(optional)*: The URL to which the callback will be sent.
- `enable_reminders` *(optional)*: A boolean value to enable reminders. If enabled the customer will receive reminders.
- `logo_url` *(optional)*: The URL of the business logo.
- `short_code` *(optional)*: The short code of the business. If not provided the default short code will be used.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerOptIn('email@example.com', '254712345678', 'https://example.com/callback', true, 'https://example.com/logo.png');

if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Update details

This is used to update the details of the business that is registered with the bill manager service.

The method takes the following parameters:

- `email`: The email address of the business, this is the email used when registering your business.
- `phone_number`: The phone number of the business, this is the phone number used when registering your business.
- `callback_url` *(optional)*: The URL to which the callback will be sent.
- `enable_reminders` *(optional)*: A boolean value to enable reminders. If enabled the customer will receive reminders.
- `logo_url` *(optional)*: The URL of the business logo.
- `short_code` *(optional)*: The short code of the business. If not provided the default short code will be used.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerUpdateDetails('email@example.com', '254712345678', 'https://example.com/callback', true, 'https://example.com/logo.png');


if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Invoices

Invoice will be created and sent to the customer. The following is the invoice format

### Single Invoice

```JSON
{
    "externalReference": "#9932340",
    "billedFullName": "John Doe",
    "billedPhoneNumber": "07XXXXXXXX",
    "billedPeriod": "August 2021",
    "invoiceName": "Jentrys",
    "dueDate": "2021-10-12",
    "accountReference": "1ASD678H",
    "amount": "800",
    "invoiceItems": [
        {
            "itemName": "food",
            "amount": "700"
        },
        {
            "itemName": "water",
            "amount": "100"
        }
    ]
}
```

### Multiple Invoices

```JSON
[
    {
        "externalReference": "#9932340",
        "billedFullName": "John Doe",
        "billedPhoneNumber": "07XXXXXXXX",
        "billedPeriod": "August 2021",
        "invoiceName": "Jentrys",
        "dueDate": "2021-10-12",
        "accountReference": "1ASD678H",
        "amount": "800",
        "invoiceItems": [
            {
                "itemName": "food",
                "amount": "700"
            },
            {
                "itemName": "water",
                "amount": "100"
            }
        ]
    },
    {
        "externalReference": "#9932341",
        "billedFullName": "John Doe",
        "billedPhoneNumber": "07XXXXXXXX",
        "billedPeriod": "August 2021",
        "invoiceName": "Jentrys",
        "dueDate": "2021-10-12",
        "accountReference": "1ASD678H",
        "amount": "800",
        "invoiceItems": [
            {
                "itemName": "food",
                "amount": "700"
            },
            {
                "itemName": "water",
                "amount": "100"
            }
        ]
    }
]
```

## Creating single invoice

This is used to create a single invoice.

The method takes the invoice data above as a parameter.

```php

use Ghostscypher\Mpesa\Facades\Mpesa;

$invoiceData = [
    "externalReference" => "#9932340",
    "billedFullName" => "John Doe",
    "billedPhoneNumber" => "07XXXXXXXX",
    "billedPeriod" => "August 2021",
    "invoiceName" => "Jentrys",
    "dueDate" => "2021-10-12",
    "accountReference" => "1ASD678H",
    "amount" => "800",
    "invoiceItems" => [
        [
            "itemName" => "food",
            "amount" => "700"
        ],
        [
            "itemName" => "water",
            "amount" => "100"
        ]
    ]
];

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerBulkInvoicing($invoiceData);


if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Creating multiple invoices

This is used to create multiple invoices.

The method takes the invoice data above as a parameter.

```php

use Ghostscypher\Mpesa\Facades\Mpesa;

$invoiceData = [
    [
        "externalReference" => "#9932340",
        "billedFullName" => "John Doe",
        "billedPhoneNumber" => "07XXXXXXXX",
        "billedPeriod" => "August 2021",
        "invoiceName" => "Jentrys",
        "dueDate" => "2021-10-12",
        "accountReference" => "1ASD678H",
        "amount" => "800",
        "invoiceItems" => [
            [
                "itemName" => "food",
                "amount" => "700"
            ],
            [
                "itemName" => "water",
                "amount" => "100"
            ]
        ]
    ],
    [
        "externalReference" => "#9932341",
        "billedFullName" => "John Doe",
        "billedPhoneNumber" => "07XXXXXXXX",
        "billedPeriod" => "August 2021",
        "invoiceName" => "Jentrys",
        "dueDate" => "2021-10-12",
        "accountReference" => "1ASD678H",
        "amount" => "800",
        "invoiceItems" => [
            [
                "itemName" => "food",
                "amount" => "700"
            ],
            [
                "itemName" => "water",
                "amount" => "100"
            ]
        ]
    ]
];

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerCreateInvoice($invoiceData);


if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Reconciliation

This is used to reconcile the invoices. Reconcile basically means to confirm that the invoices have been paid.

The method takes the following parameters:
        string $transaction_id,
        int $paid_amount,
        string $msisdn,
        string $date_created,
        string $account_reference,
        ?string $short_code = null

- `transaction_id`: The transaction ID of the payment. E.g. the Mpesa transaction ID. LX123456789.
- `paid_amount`: The amount that was paid.
- `msisdn`: The phone number of the customer.
- `date_created`: The date the payment was made. E.g. 2021-10-12.
- `account_reference`: The account reference of the invoice. E.g. 1ASD678H.
- `short_code` *(optional)*: The short code of the business. If not provided the default short code will be used.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerReconciliation('LX123456789', 800, '254712345678', '2021-10-12', '1ASD678H');

if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Cancel Single Invoice

This is used to cancel a single invoice.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$invoiceData = [
    "externalReference" => "#9932340",
];

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerCancelSingleInvoicing($invoiceData);


if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```

## Cancel Bulk Invoices

This is used to cancel multiple invoices.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$invoiceData = [
    [
        "externalReference" => "#9932340",
    ],
    [
        "externalReference" => "#9932341",
    ]
];

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::billManagerCancelBulkInvoicing($invoiceData);


if($response->successful()) {
    // Success
    $response = $response->json();
} else {
    // Error
    $response = $response->json();
}
```
