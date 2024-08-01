<?php

use Ghostscypher\Mpesa\Facades\Mpesa;

it('can be instantiated', function () {
    $mp = new \Ghostscypher\Mpesa\Mpesa();
    expect($mp)->toBeInstanceOf(\Ghostscypher\Mpesa\Mpesa::class);
});

it('can be instantiated via facade', function () {
    $mp = Mpesa::getFacadeRoot();
    expect($mp)->toBeInstanceOf(\Ghostscypher\Mpesa\Mpesa::class);
});

it('can be instantiated via service provider', function () {
    $mp = app('mpesa');
    expect($mp)->toBeInstanceOf(\Ghostscypher\Mpesa\Mpesa::class);
});
