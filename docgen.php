<?php

use Ghostscypher\Mpesa\Mpesa;

return [
    'facade' => Ghostscypher\Mpesa\Facades\Mpesa::class,

    // Optional
    'classes' => [
        Mpesa::class,
    ],

    // Global Excluded Methods
    'excludedMethods' => [],
];
