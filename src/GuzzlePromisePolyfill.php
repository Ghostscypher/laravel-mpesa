<?php

namespace GuzzleHttp\Promise;

// Polyfill for GuzzleHttp\Promise\Create::promiseFor for Laravel 7
if (! function_exists('\GuzzleHttp\Promise\promise_for')) {

    /**
     * @inheritDoc
     * @param mixed $response
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    function promise_for($response):\GuzzleHttp\Promise\PromiseInterface
    {
        return \GuzzleHttp\Promise\Create::promiseFor($response);
    }
}
