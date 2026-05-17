<?php

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

it('can send stkpush', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::stkPush('254708374149', 1, 'Test Account');

    expect($response)->toBeInstanceOf(Response::class);
});
