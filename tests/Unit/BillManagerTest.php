<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can opt in', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerOptIn('email@test.com', '254700000000', 'https://example.com/timeout', true, 'https://example.com/timeout');

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can update details', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerUpdateDetails('email@test.com', '254700000000', 'https://example.com/timeout');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can subscribe to single invoice', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerSingleInvoicing([
        'externalReference' => '#9932340',
        'billedFullName' => 'John Doe',
        'billedPhoneNumber' => '0700000000',
        'billedPeriod' => 'August 2021',
        'invoiceName' => 'Jentrys',
        'dueDate' => '2021-10-12',
        'accountReference' => '1ASD678H',
        'amount' => '800',
        'invoiceItems' => [
            [
                'itemName' => 'food',
                'amount' => '700',
            ],
            [
                'itemName' => 'water',
                'amount' => '100',
            ],
        ],
    ]);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can subscribe to bulk invoice', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerBulkInvoicing([
        [
            'externalReference' => '#9932340',
            'billedFullName' => 'John Doe',
            'billedPhoneNumber' => '0700000000',
            'billedPeriod' => 'August 2021',
            'invoiceName' => 'Jentrys',
            'dueDate' => '2021-10-12',
            'accountReference' => '1ASD678H',
            'amount' => '800',
            'invoiceItems' => [
                [
                    'itemName' => 'food',
                    'amount' => '700',
                ],
                [
                    'itemName' => 'water',
                    'amount' => '100',
                ],
            ],
        ],
        [
            'externalReference' => '#9932340',
            'billedFullName' => 'John Doe',
            'billedPhoneNumber' => '0700000000',
            'billedPeriod' => 'August 2021',
            'invoiceName' => 'Jentrys',
            'dueDate' => '2021-10-12',
            'accountReference' => '1ASD678H',
            'amount' => '800',
            'invoiceItems' => [
                [
                    'itemName' => 'food',
                    'amount' => '700',
                ],
                [
                    'itemName' => 'water',
                    'amount' => '100',
                ],
            ],
        ],
    ]);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can reconcile invoice', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerReconciliation('LXXXXX', 1, '254700000000', '2024-08-01', 'test');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can cancel single invoice', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerCancelSingleInvoicing([
        'externalReference' => '#9932340',
    ]);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can cancel bulk invoice', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::billManagerCancelBulkInvoicing([
        [
            'externalReference' => '#9932340',
        ],
        [
            'externalReference' => '#9932341',
        ],
    ]);

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
