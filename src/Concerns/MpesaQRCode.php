<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;

trait MpesaQRCode
{
    use MpesaAuth;

    /**
     * Generate the QR code
     *
     * @param string $merchant_name The name of the merchant
     * @param string $ref_no The reference number
     * @param int $amount The amount to pay
     * @param ?string $CPI The Consumer Paybill Number
     * @param string $trx_code The transaction code to use
     *  - BG: Buy Goods)
     *  - WA: Withdrawal at Agent till)
     *  - PB: Paybill
     *  - SM: Send Money (Mobile number)
     *  - SB: Sent to Business. Business number CPI in MSISDN format.
     * @param int $size The size of the QR code
     *
     * @return \Illuminate\Http\Client\Response
     */
    public function generateQRCode(
        string $merchant_name,
        string $ref_no,
        int $amount,
        ?string $CPI = null,
        string $trx_code = 'PB',
        int $size = 300
    ): \Illuminate\Http\Client\Response {
        $this->generateToken();

        $data = [
            'MerchantName' => $merchant_name,
            'Amount' => $amount,
            'RefNo' => $ref_no,
            'CPI' => $CPI ?? $this->short_code,
            'TrxCode' => $trx_code,
            'Size' => $size,
        ];
        
        $validator = self::validate($data, [
            'MerchantName' => 'required|string',
            'Amount' => 'required|integer|min:1',
            'RefNo' => 'required|string',
            'CPI' => 'required|string',
            'TrxCode' => 'required|string',
            'Size' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/qrcode/v1/generate', $validator->validated());
    }
}
