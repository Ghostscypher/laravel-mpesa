<?php

// Test authorization
use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can send B2C request', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::B2C('254708374149', 500, 'https://example.com/timeout', 'https://example.com/result');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});

it("can send B2C Ratiba request", function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::ratiba("lipa mdogo", "254708374149", 500, "20250101", "20250131", 4, "January 2025 Payment");

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
