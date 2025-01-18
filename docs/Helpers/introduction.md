---
title: Helper functions
weight: 1
---

## Helper functions

The package comes with a few helper functions that you can use to make your life easier. These functions are:

## Constants

```php
<?php

namespace Ghostscypher\Mpesa\Helpers;

class Constants
{
    // Identifier types
    public const MPESA_IDENTIFIER_TYPE_MSISDN = '1';

    public const MPESA_IDENTIFIER_TYPE_TILL = '2';

    public const MPESA_IDENTIFIER_TYPE_PAYBILL = '4';

    public const MPESA_IDENTIFIER_TYPE_SHORTCODE = '4';

    public const MPESA_IDENTIFIER_TYPE_REVERSAL = '11';

    // Command ids
    public const MPESA_COMMAND_ID_TRANSACTION_REVERSAL = 'TransactionReversal';

    public const MPESA_COMMAND_ID_SALARY_PAYMENT = 'SalaryPayment';

    public const MPESA_COMMAND_ID_BUSINESS_PAYMENT = 'BusinessPayment';

    public const MPESA_COMMAND_ID_PROMOTION_PAYMENT = 'PromotionPayment';

    public const MPESA_COMMAND_ID_ACCOUNT_BALANCE = 'AccountBalance';

    public const MPESA_COMMAND_ID_CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    public const MPESA_COMMAND_ID_CUSTOMER_BUY_GOODS_ONLINE = 'CustomerBuyGoodsOnline';

    public const MPESA_COMMAND_ID_TRANSACTION_STATUS_QUERY = 'TransactionStatusQuery';

    public const MPESA_COMMAND_ID_CHECK_IDENTITY = 'CheckIdentity';

    public const MPESA_COMMAND_ID_BUSINESS_PAY_BILL = 'BusinessPayBill';

    public const MPESA_COMMAND_ID_BUSINESS_PAY_BUY_GOODS = 'BusinessBuyGoods';

    public const MPESA_COMMAND_ID_DISBURSE_FUNDS_TO_BUSINESS = 'DisburseFundsToBusiness';

    public const MPESA_COMMAND_ID_BUSINESS_TO_BUSINESS_TRANSFER = 'BusinessToBusinessTransfer';

    public const MPESA_COMMAND_ID_TRANSFER_FROM_MMF_TO_UTILITY = 'BusinessTransferFromMMFToUtility';

    public const MPESA_COMMAND_ID_MERCHANT_TO_MERCHANT_TRANSFER = 'MerchantToMerchantTransfer';

    public const MPESA_COMMAND_ID_MERCHANT_FROM_MERCHANT_TO_WORKING = 'MerchantTransferFromMerchantToWorking';

    public const MPESA_COMMAND_ID_MERCHANT_TO_MMF = 'MerchantServicesMMFAccountTransfer';

    public const MPESA_COMMAND_ID_AGENCY_FLOAT_ADVANCE = 'AgencyFloatAdvance';
}
```

## AllowOnlyWhitelistedIps Middleware

```php

<?php

namespace Ghostscypher\Mpesa\Http\Middleware;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AllowOnlyWhitelistedIps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  mixed  $roles
     */
    public function handle(Request $request, \Closure $next, ?bool $fuzzy_match = null): Response
    {
        // If localhost or environment is local
        if (app()->environment(config('mpesa.allowed_environments'))) {
            return $next($request);
        }

        $whitelisted_ips = config('mpesa.whitelisted_ips');
        $client_ip = $request->ip();

        if (is_null($fuzzy_match)) {
            $fuzzy_match = config('mpesa.allow_fuzzy_matching');
        }

        // If not a fuzzy match and the client IP is not in the whitelist, abort
        if (! $fuzzy_match && ! in_array($client_ip, $whitelisted_ips)) {
            abort(404, 'Not Found');
        } elseif ($fuzzy_match) {
            // If it is a fuzzy match, check if the client IP is in the whitelist
            $is_whitelisted = false;
            $fuzzy_match_ips = config('mpesa.fuzzy_match_ips'); // Get the IPs to fuzzy match against
            foreach ($fuzzy_match_ips as $ip) {
                if (strpos($client_ip, $ip) === 0) {
                    $is_whitelisted = true;

                    break;
                }
            }

            if (! $is_whitelisted) {
                abort(404, 'Not Found');
            }
        }

        return $next($request);
    }
}
```


## Send raw request

You can send a raw request to the Mpesa API using the `rawRequest` method. 

This method takes the following parameters:

- `$method`: The HTTP method to use. This can be `GET`, `POST`, `PUT`, `PATCH`, `DELETE`.
- `$url`: The URL to send the request to.
- `$data`: The data to send with the request. This can be an array or a string. Defaults to an empty array.
- `$headers`: The headers to send with the request. Defaults to an empty array.
- `$authenticated`: Whether to send the request with authentication. Defaults to `true`.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

/**
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaAuthException
 * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException
 */
$response = Mpesa::rawRequest('GET', '/v1/billmanager-invoice/optin', [], ['Content-Type' => 'application/json'], true);

// Or supply full URL
$response = Mpesa::rawRequest('GET', 'https://sandbox.safaricom.co.ke/v1/billmanager-invoice/optin', [], ['Content-Type' => 'application/json'], true);

```

## Format number to E.164 format without the leading plus

You can format a phone number to E.164 format without the leading plus e.g. `070000000000` to `254700000000` using the `formatPhoneNumber` method.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$phone_number = '070000000000';

$formatted_phone_number = Mpesa::formatPhoneNumberE164($phone_number);

// Output: 254700000000
```

## Check if Mpesa is running in production or sandbox

You can check if Mpesa is running in production or sandbox using the `isProduction` method.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$is_production = Mpesa::isProduction();
$is_sandbox = Mpesa::isSandbox();
```

## Parse the mpesa balance

Given a string such as

```text
Working Account|KES|700000.00|700000.00|0.00|0.00&Float
                        Account|KES|0.00|0.00|0.00|0.00&Utility
                        Account|KES|228037.00|228037.00|0.00|0.00&Charges Paid
                        Account|KES|-1540.00|-1540.00|0.00|0.00&Organization Settlement
                        Account|KES|0.00|0.00|0.00|0.00

```

You can parse the balance using the `parseMpesaBalance` method to return an array of the balances.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$balance = 'Working Account|KES|700000.00|700000.00|0.00|0.00&Float
                        Account|KES|0.00|0.00|0.00|0.00&Utility
                        Account|KES|228037.00|228037.00|0.00|0.00&Charges Paid
                        Account|KES|-1540.00|-1540.00|0.00|0.00&Organization Settlement
                        Account|KES|0.00|0.00|0.00|0.00';

$balances = Mpesa::parseMpesaBalance($balance);

                'account' => $item[0],
                'currency' => $item[1],
                'available_balance' => $item[2],
                'current_balance' => $item[3],
                'reserved_balance' => $item[4],
                'unclear_balance' => $item[5],

// Output: [
//     'Working Account' => [
//         'account' => 'Working Account',
//         'currency' => 'KES',
//         'available_balance' => 700000.00,
//         'current_balance' => 700000.00,
//         'reserved_balance' => 0.00,
//         'unclear_balance' => 0.00,
//     ],
//     'Account' => [
//         'account' => 'Account',
//         'currency' => 'KES',
//         'available_balance' => 0.00,
//         'current_balance' => 0.00,
//         'reserved_balance' => 0.00,
//         'unclear_balance' => 0.00,
//     ],
//     ...
// ]
```

## Deconstruct data

You can deconstruct data using the `deconstructData` method.

This method allows you to deconstruct data from an array or object into a flat array.
For example given the following data returned from an API, and we want to get the data
in the CallbackMetadata.Item

```json
{
    "Body": {
        "stkCallback": {
            "MerchantRequestID": "29115-34620561-1",
            "CheckoutRequestID": "ws_CO_04082024120745239743456815",
            "ResultCode": 0,
            "ResultDesc": "The service request is processed successfully.",
            "CallbackMetadata": {
                "Item": [
                    {
                        "Name": "Amount",
                        "Value": 2
                    },
                    {
                        "Name": "MpesaReceiptNumber",
                        "Value": "NLT7RT45SR"
                    },
                    {
                        "Name": "TransactionDate",
                        "Value": 20191219102115
                    },
                    {
                        "Name": "PhoneNumber",
                        "Value": 254700000000
                    }
                ]
            }
        }
    }
}
```

We can deconstruct the data as follows:

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$data = [
    'Body' => [
        'stkCallback' => [
            'MerchantRequestID' => '29115-34620561-1',
            'CheckoutRequestID' => 'ws_CO_04082024120745239743456815',
            'ResultCode' => 0,
            'ResultDesc' => 'The service request is processed successfully.',
            'CallbackMetadata' => [
                'Item' => [
                    [
                        'Name' => 'Amount',
                        'Value' => 2
                    ],
                    [
                        'Name' => 'MpesaReceiptNumber',
                        'Value' => 'NLT7RT45SR'
                    ],
                    [
                        'Name' => 'TransactionDate',
                        'Value' => 20191219102115
                    ],
                    [
                        'Name' => 'PhoneNumber',
                        'Value' => 254700000000
                    ]
                ]
            ]
        ]
    ]
];

$deconstructed_data = Mpesa::deconstructData(
    $data['Body']['stkCallback']['CallbackMetadata']['item'], // The data to deconstruct
    'Name', // The key to use as the key in the new array
    'Value' // The key to use as the value in the new array
);

// Output: [
//     'Amount' => 2,
//     'MpesaReceiptNumber' => 'NLT7RT45SR',
//     'TransactionDate' => 20191219102115,
//     'PhoneNumber' => 254700000000,
// ]
```

## Generate a callback URL

This method allows you to generate a callback URL for your application. You can pass in the full route,
route name, or even partial path.

```php
use Ghostscypher\Mpesa\Facades\Mpesa;

$named_url = Mpesa::generateCallbackUrl('route.name', ['param1' => 'value1', 'param2' => 'value2']);
$current_url = Mpesa::generateCallbackUrl('/path/to/route', ['param1' => 'value1', 'param2' => 'value2']);
$full_url = Mpesa::generateCallbackUrl('https://example.com/path/to/route', ['param1' => 'value1', 'param2' => 'value2']);

// Output: 'https://example.com/path/to/route?param1=value1&param2=value2'
```
