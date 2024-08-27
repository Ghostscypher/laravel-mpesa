<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can register validation and confirmation url', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::registerUrl('https://example.com/validation', 'https://example.com/confirmation');

    expect($response->json())->toHaveKey('ResponseCode', '0');
});
