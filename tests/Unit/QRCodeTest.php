<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Support\Facades\Http;

it('can send generate QR code', function () {
    Http::fake([
        '*' => Http::response([
            'ResponseDescription' => 'success',
            'ResponseCode' => '0',
            'access_token' => 'test_token',
            'expires_in' => 3600,
        ], 200),
    ]);

    $response = Mpesa::generateQRCode('Algoskech Solutions', 'test QR code', 1, '4072835', 'PB', 300);

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
