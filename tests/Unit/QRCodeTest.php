<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can send generate QR code', function () {
    $response = Mpesa::generateQRCode("Daraja-Sandbox", "REF-1234", "1", "174379", "PB", 300);

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
