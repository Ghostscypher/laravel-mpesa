<?php

namespace GuzzleHttp\Promise;

// Polyfill for GuzzleHttp\Promise\Create::promiseFor for Laravel 7
if (! function_exists('\GuzzleHttp\Promise\promise_for')) {
    function promise_for($response)
    {
        return class_exists(\GuzzleHttp\Promise\Create::class)
            ? \GuzzleHttp\Promise\Create::promiseFor($response)
            : \GuzzleHttp\Promise\promise_for($response);
    }
}
