<?php

namespace Ghostscypher\Mpesa\Events;

use Illuminate\Http\Request;

class MpesaCallbackReceived
{
    public Request $request;

    public string $type;

    public function __construct(Request $request, string $type)
    {
        $this->request = $request;
        $this->type = $type;
    }
}
