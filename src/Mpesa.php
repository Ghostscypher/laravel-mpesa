<?php

namespace Ghostscypher\Mpesa;

use Ghostscypher\Mpesa\Concerns\MpesaStkPush;
use Illuminate\Http\Client\Response;

/**
 *
 * Class Mpesa
 *
 * This class will be used to interact with the Safaricom APIs
 */
class Mpesa
{
    use MpesaStkPush;

    /**
     * If for some reason you need to make a raw request to the APIs you can use this method
     *
     * @param string $method
     * @param string $url
     * @param array $data
     * @param bool $authenticated
     *
     * @return Response
     */
    public function rawRequest(string $method, string $url, array $data = [], array $headers = [], bool $authenticated = true): Response
    {
        if($authenticated) {
            $this->generateToken();
        }

        $response = $this->http_client
            ->replaceHeaders($headers)
            ->send($method, $url, $data);

        return $response;
    }
}
