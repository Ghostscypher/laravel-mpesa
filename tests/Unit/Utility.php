<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can send raw request', function () {
    $response = Mpesa::rawRequest('GET', 'https://sandbox.safaricom.co.ke/', [], [], false);

    // Expect no exception
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
});
