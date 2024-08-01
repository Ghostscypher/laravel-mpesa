<?php

namespace Ghostscypher\Mpesa\Listeners;

use Illuminate\Http\Client\Events\ResponseReceived;

class LogResponseReceived
{
    /**
     * Handle the given event.
     */
    public function handle(ResponseReceived $event): void
    {
        if(config('mpesa.features.enable_logging')) {
            $request = $event->request;
            $response = $event->response;
            
            if($request->hasHeader('X-Ghostscypher-Laravel-Mpesa-Request-ID')) {
                app(config('mpesa.models.log'))->updateOrCreate([
                    'x_reference_id' => $request->header('X-Ghostscypher-Laravel-Mpesa-Request-ID')[0],
                ], [
                    'response_headers' => $response->headers(),
                    'response_status' => $response->status(),
                    'response_body' => $response->body(),
                ]);
            }
        }
    }
}
