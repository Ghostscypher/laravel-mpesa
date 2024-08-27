<?php

// Test authorization

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can generate correct callback url with parameters', function () {
    $url = Mpesa::generateCallbackUrl('https://example.com/result?param1=1&param2=2');

    // Expect the URL to be correct
    expect($url)->toBe('https://example.com/result?param1=1&param2=2');
});

it('can generate correct callback url with no parameters', function () {
    $url = Mpesa::generateCallbackUrl('https://example.com/result');

    // Expect the URL to be correct
    expect($url)->toBe('https://example.com/result');
});

it('can generate correct callback url named routes', function () {
    // Set the base URL
    app('url')->forceRootUrl('http://pest.test');

    $url = Mpesa::generateCallbackUrl('named_route_test');

    // Expect the URL to be correct
    expect($url)->toBe('http://pest.test/test');
});

it('can generate correct callback url named routes with parameters', function () {
    // Set the base URL
    app('url')->forceRootUrl('http://pest.test');

    $url = Mpesa::generateCallbackUrl('named_route_test?param1=1&param2=2');

    // Expect the URL to be correct
    expect($url)->toBe('http://pest.test/test?param1=1&param2=2');
});

it('can deconstruct data', function () {
    $data = [
        ['Name' => 'key1', 'Value' => 'value1'],
        ['Name' => 'key2', 'Value' => 'value2'],
        ['Name' => 'key3', 'Value' => 'value3'],
    ];

    $deconstructed = Mpesa::deconstructData($data)->toArray();

    // Expect the deconstructed data in both arrays to be the same
    expect($deconstructed)->toBe([
        'key1' => 'value1',
        'key2' => 'value2',
        'key3' => 'value3',
    ]);
});

it('can deconstruct data with case insensitive keys', function () {
    $data = [
        ['Name' => 'Key1', 'Value' => 'value1'],
        ['Name' => 'key2', 'Value' => 'Value2'],
        ['Name' => 'Key3', 'Value' => 'value3'],
    ];

    $deconstructed = Mpesa::deconstructData($data, 'Name', 'Value', true)->toArray();

    // Expect the deconstructed data to be correct
    expect($deconstructed)->toBe([
        'key1' => 'value1',
        'key2' => 'Value2',
        'key3' => 'value3',
    ]);
});

it('can deconstruct data with missing key', function () {
    $data = [
        ['Name' => 'key1', 'Value' => 'value1'],
        ['Value' => 'value2'],
        ['Name' => 'key3', 'Value' => 'value3'],
    ];

    $deconstructed = Mpesa::deconstructData($data)->toArray();

    // Expect the deconstructed data to be correct
    expect($deconstructed)->toBe([
        'key1' => 'value1',
        'key3' => 'value3',
    ]);
});

it('can deconstruct data with missing key and value', function () {
    $data = [
        ['Name' => 'key1', 'Value' => 'value1'],
        ['Value' => 'value2'],
        ['Name' => 'key3'],
    ];

    $deconstructed = Mpesa::deconstructData($data)->toArray();

    // Expect the deconstructed data to be correct
    expect($deconstructed)->toBe([
        'key1' => 'value1',
        'key3' => null,
    ]);
});

it('can parse mpesa balance', function () {
    $balance = 'Account1|KES|1000.00|1000.00|0.00|0.00&Account2|KES|2000.00|2000.00|0.00|0.00';

    $parsed = Mpesa::parseMpesaBalance($balance);

    // Expect the parsed data to be correct
    expect($parsed)->toBe([
        'Account1' => [
            'account' => 'Account1',
            'currency' => 'KES',
            'available_balance' => '1000.00',
            'current_balance' => '1000.00',
            'reserved_balance' => '0.00',
            'unclear_balance' => '0.00',
        ],
        'Account2' => [
            'account' => 'Account2',
            'currency' => 'KES',
            'available_balance' => '2000.00',
            'current_balance' => '2000.00',
            'reserved_balance' => '0.00',
            'unclear_balance' => '0.00',
        ],
    ]);
});
