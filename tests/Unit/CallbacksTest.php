<?php

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can register webhook', function () {
    $default_routes = [
        'stk_push_callback_url' => '/lmp/stk/push/callback',
        'c2b_validation_url' => '/lmp/c2b/validation',
        'c2b_confirmation_url' => '/lmp/c2b/confirmation',
        'b2c_result_url' => '/lmp/b2c/result',
        'b2c_timeout_url' => '/lmp/b2c/timeout',
        'b2b_result_url' => '/lmp/b2b/result',
        'b2b_timeout_url' => '/lmp/b2b/timeout',
        'b2b_stk_callback_url' => '/lmp/b2b/stk/callback',
        'status_result_url' => '/lmp/status/result',
        'status_timeout_url' => '/lmp/status/timeout',
        'reversal_result_url' => '/lmp/reversal/result',
        'reversal_timeout_url' => '/lmp/reversal/timeout',
        'balance_result_url' => '/lmp/balance/result',
        'balance_timeout_url' => '/lmp/balance/timeout',
        'bill_manager_callback_url' => '/lmp/bill/manager/callback',
    ];

    // If the route is not set in the config file, use the default route
    foreach ($default_routes as $key => $route) {
        if (empty(config('mpesa.'.$key))) {
            // Url + route
            config(['mpesa.'.$key => $route]);
        }
    }

    // Check if the routes are registered
    expect(config('mpesa.stk_push_callback_url'))->toBe('/lmp/stk/push/callback');
    expect(config('mpesa.c2b_validation_url'))->toBe('/lmp/c2b/validation');
    expect(config('mpesa.c2b_confirmation_url'))->toBe('/lmp/c2b/confirmation');
    expect(config('mpesa.b2c_result_url'))->toBe('/lmp/b2c/result');
    expect(config('mpesa.b2c_timeout_url'))->toBe('/lmp/b2c/timeout');
    expect(config('mpesa.b2b_result_url'))->toBe('/lmp/b2b/result');
    expect(config('mpesa.b2b_timeout_url'))->toBe('/lmp/b2b/timeout');
    expect(config('mpesa.b2b_stk_callback_url'))->toBe('/lmp/b2b/stk/callback');
    expect(config('mpesa.status_result_url'))->toBe('/lmp/status/result');
    expect(config('mpesa.status_timeout_url'))->toBe('/lmp/status/timeout');
    expect(config('mpesa.reversal_result_url'))->toBe('/lmp/reversal/result');
    expect(config('mpesa.reversal_timeout_url'))->toBe('/lmp/reversal/timeout');
    expect(config('mpesa.balance_result_url'))->toBe('/lmp/balance/result');
    expect(config('mpesa.balance_timeout_url'))->toBe('/lmp/balance/timeout');
    expect(config('mpesa.bill_manager_callback_url'))->toBe('/lmp/bill/manager/callback');
});

it('can generate correct callback url when named route is set in config', function () {
    // Set the base URL
    app('url')->forceRootUrl('http://pest.test');

    // Set the named route
    config(['mpesa.stk_push_callback_url' => 'named_route_test']);

    $url = Mpesa::generateCallbackUrl(config('mpesa.stk_push_callback_url'));

    // Expect the response to be successful
    expect($url)->toBe('http://pest.test/test');
});

it('can generate correct callback url when named route is not set in config', function () {
    // Set the base URL
    app('url')->forceRootUrl('http://pest.test');

    // Set the named route
    config(['mpesa.stk_push_callback_url' => '/test/123']);

    $url = Mpesa::generateCallbackUrl(config('mpesa.stk_push_callback_url'));

    // Expect the response to be successful
    expect($url)->toBe('http://pest.test/test/123');
});
