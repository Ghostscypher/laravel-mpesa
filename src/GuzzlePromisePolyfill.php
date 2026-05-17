<?php

namespace GuzzleHttp\Promise;

// Polyfill for GuzzleHttp\Promise\Create::promiseFor for Laravel 7
if (! function_exists('\GuzzleHttp\Promise\promise_for')) {

    /**
     * {@inheritDoc}
     *
     * @param  mixed  $response
     */
    function promise_for($response): PromiseInterface
    {
        return Create::promiseFor($response);
    }
}
