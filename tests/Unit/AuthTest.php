<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can generate auth token', function () {
    $token = Mpesa::generateToken(true);

    // Expect no exception
    expect($token)->toBeString();
});


it('can use previously generated token from token table', function () {
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
