<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can send raw request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::rawRequest('GET', 'https://sandbox.safaricom.co.ke/', [], [], false);

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can send account balance request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::checkBalance('https://example.com/result', 'https://example.com/timeout');

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can send reversal request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::reverseTransaction('OEI2AK4Q16', '1', 'https://example.com/result', 'https://example.com/timeout', '11', '600992');

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can send transaction status request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::transactionStatus('OEI2AK4Q16', '4', 'https://example.com/result', 'https://example.com/timeout', '600992');

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
