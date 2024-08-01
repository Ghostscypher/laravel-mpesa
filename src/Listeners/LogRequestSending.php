<?php

namespace Ghostscypher\Mpesa\Listeners;

use Illuminate\Http\Client\Events\RequestSending;

class LogRequestSending
{
    /**
     * Handle the given event.
     */
    public function handle(RequestSending $event): void
    {
        if(config('mpesa.features.enable_logging')) {
            $request = $event->request;
            
            if($request->hasHeader('X-Ghostscypher-Laravel-Mpesa-Request-ID')) {
                app(config('mpesa.models.log'))->updateOrCreate([
                    'x_reference_id' => $request->header('X-Ghostscypher-Laravel-Mpesa-Request-ID')[0],
                ], [
                    'endpoint' => $request->url(),
                    'request_headers' => $request->headers(),
                    'request_data' => $request->body(),
                    'request_method' => $request->method(),
                    'environment' => config('mpesa.env') ?? 'sandbox',
                ]);
            }
        }
    }
}
