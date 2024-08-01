<?php

use Ghostscypher\Mpesa\Facades\Mpesa;

// it('can send stkpush', function () {
//     $response = Mpesa::stkPush('254708263715', 1, 'Test Account', 'https://example.com/callback');

//     expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
//     expect($response->status())->toBe(200);
//     expect($response->json())->toBeArray();
//     expect($response->json())->toHaveKeys([
//         'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
//         'ResponseDescription', 'CustomerMessage',
//     ]);
// });

// it('can query stkpush status', function () {
//     $response = Mpesa::stkPushQuery('ws_CO_01082024234532570708263715');
//     expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
//     expect($response->status())->toBe(200);
//     expect($response->json())->toBeArray();
//     expect($response->json())->toHaveKeys([
//         'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
//         'ResponseDescription', 'ResultCode', 'ResultDesc',
//     ]);
// });
