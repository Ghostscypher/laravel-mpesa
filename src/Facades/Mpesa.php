<?php

namespace Ghostscypher\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ghostscypher\Mpesa\Mpesa
 */
class Mpesa extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor():string
    {
        return 'mpesa';
    }
}
