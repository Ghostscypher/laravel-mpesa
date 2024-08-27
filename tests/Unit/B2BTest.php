<?php

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can send B2B request', function () {
    Http::fake([
        '*' => Http::response([
            'ConversationID' => 'AG_20210929_00007f7b7b7b',
            'OriginatorCoversationID' => 'AG_20210929_00007f7b7b7b',
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::B2B('174379', 500, 'Test', 'https://example.com/timeout', 'https://example.com/result');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it('can send B2B STK request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::B2BStkPush('174379', 10, 'Test', 'https://example.com/callback', '7318002');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
