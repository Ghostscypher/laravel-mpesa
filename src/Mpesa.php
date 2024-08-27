<?php

namespace Ghostscypher\Mpesa;

use Ghostscypher\Mpesa\Concerns\MpesaB2B;
use Ghostscypher\Mpesa\Concerns\MpesaB2C;
use Ghostscypher\Mpesa\Concerns\MpesaBillManager;
use Ghostscypher\Mpesa\Concerns\MpesaC2B;
use Ghostscypher\Mpesa\Concerns\MpesaQRCode;
use Ghostscypher\Mpesa\Concerns\MpesaStkPush;
use Ghostscypher\Mpesa\Concerns\MpesaUtility;
use Illuminate\Http\Client\Response;

/**
 * Class Mpesa
 *
 * This class will be used to interact with the Safaricom APIs
 */
class Mpesa
{
    use MpesaB2B;
    use MpesaB2C;
    use MpesaBillManager;
    use MpesaC2B;
    use MpesaQRCode;
    use MpesaStkPush;
    use MpesaUtility;

    /**
     * If for some reason you need to make a raw request to the APIs you can use this method
     */
    public function rawRequest(string $method, string $url, array $data = [], array $headers = [], bool $authenticated = true): Response
    {
        if ($authenticated) {
            $this->generateToken();
        }

        $response = self::updateHttpClient($headers)
            ->send($method, $url, $data);

        return $response;
    }
}
