<?php

namespace GuzzleHttp\Promise;

require_once __DIR__.'/../vendor/autoload.php';

// Polyfill for GuzzleHttp\Promise\Create::promiseFor for Laravel 7
if (! function_exists('\GuzzleHttp\Promise\promise_for')) {
    function promise_for($response)
    {
        return class_exists(\GuzzleHttp\Promise\Create::class)
            ? \GuzzleHttp\Promise\Create::promiseFor($response)
            : \GuzzleHttp\Promise\promise_for($response);
    }
}
