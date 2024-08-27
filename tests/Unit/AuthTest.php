<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can generate auth token', function () {
    Http::fake([
        '*' => Http::response([
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $token = Mpesa::generateToken(true);

    // Expect no exception
    expect($token)->toBeString();
});

it('can use previously generated token from token table', function () {
    Http::fake([
        '*' => Http::response([
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $token = Mpesa::generateToken();

    // Expect no exception
    expect($token)->toBeString();
});

it('can throw auth exception when client key or client secret is not supplied', function () {
    // Expect might throw an exception
    config(['mpesa.consumer_key' => '']);

    expect(function () {
        Mpesa::generateToken(true);
    })->toThrow(\Ghostscypher\Mpesa\Exceptions\MpesaAuthException::class);
});

it('can generate security credentials', function () {
    $credentials = Mpesa::generateSecurityCredential();

    // Expect no exception
    expect($credentials)->toBeString();
});
