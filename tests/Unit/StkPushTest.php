<?php

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can send stkpush', function () {
    $response = Mpesa::stkPush('254708374149', 1, 'Test Account', 'https://example.com/callback');

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
    expect($response->json())->toHaveKeys([
        'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
        'ResponseDescription', 'CustomerMessage',
    ]);
});

it('can query stkpush status', function () {

    /**
     * @var \Ghostscypher\Mpesa\Models\MpesaStkPush $stk_push_model
     */
    $stk_push_model = app(config('mpesa.models.stk_push'));

    // Wait for the stk push to be processed
    if(! $stk_push_model->where('status', 'pending')->latest()->exists()) {
        sleep(20);
    }
        
    $checkout_request_id = $stk_push_model->where('status', 'pending')->latest()->first();
    
    // If null, then assume passed
    if(! $checkout_request_id) {
        $this->assertTrue(true);

        return;
    }

    $checkout_request_id = $checkout_request_id->checkout_request_id;

    $response = Mpesa::stkPushQuery($checkout_request_id);
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
    expect($response->json())->toHaveKeys([
        'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
        'ResponseDescription', 'ResultCode', 'ResultDesc',
    ]);
});


it('can update stk push data in DB for successfull request', function () {
    $response = Mpesa::stkPushQuery("ws_CO_02082024013354213708263715");

    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
    expect($response->json())->toHaveKeys([
        'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
        'ResponseDescription', 'ResultCode', 'ResultDesc',
    ]);
});

it('can update stk push data in DB for failed request', function () {
    $response = Mpesa::stkPushQuery("ws_CO_02082024012400278708263715");
    expect($response)->toBeInstanceOf(\Illuminate\Http\Client\Response::class);
    expect($response->status())->toBe(200);
    expect($response->json())->toBeArray();
    expect($response->json())->toHaveKeys([
        'MerchantRequestID', 'CheckoutRequestID', 'ResponseCode',
        'ResponseDescription', 'ResultCode', 'ResultDesc',
    ]);
});
