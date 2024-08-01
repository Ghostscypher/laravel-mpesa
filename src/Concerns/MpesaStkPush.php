<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;

trait MpesaStkPush
{
    use MpesaAuth;

    /**
     * Generate the STK password
     * @param mixed $timestamp The timestamp to use for the password in the format YmdHis
     *
     * @return string
     */
    protected function generateStkPassword(string $timestamp): string
    {
        return base64_encode("{$this->short_code}{$this->passkey}{$timestamp}");
    }

    /**
     * @param string $phone_number The phone number to send the STK push to (MSISDN)
     * @param int $amount The amount to request from the user, minimum is 1
     * @param string $account_reference The reference number for the transaction, this the account number for the transaction
     * @param string $callback_url The URL to send the callback to, if null we will use the default callback URL
     * @param string $description The description of the transaction
     * @param string $transaction_type The type of transaction, default is CustomerPayBillOnline which is used for paybill numbers, if using a till number use CustomerBuyGoodsOnline
     *
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException If the validation fails
     */
    public function stkPush(string $phone_number, int $amount, string $account_reference = '', string $callback_url = null, string $description = 'Description', string $transaction_type = 'CustomerPayBillOnline'): \Illuminate\Http\Client\Response
    {
        // Generate the token
        $this->generateToken();

        // Timestamp
        $timestamp = date('YmdHis');

        // Data
        $data = [
            'BusinessShortCode' => $this->short_code,
            'Password' => $this->generateStkPassword($timestamp),
            'Timestamp' => $timestamp,
            'TransactionType' => $transaction_type,
            'Amount' => $amount,
            'PartyA' => $phone_number,
            'PartyB' => $this->short_code,
            'PhoneNumber' => $phone_number,
            'CallBackURL' => $callback_url ?? config('mpesa.callback_url'),
            'AccountReference' => $account_reference,
            'TransactionDesc' => $description,
        ];

        // Validate the parameters
        $validator = self::validator($data, [
            'BusinessShortCode' => 'required|numeric',
            'Password' => 'required|string',
            'Timestamp' => 'required|date_format:YmdHis',
            'TransactionType' => 'required|string|in:CustomerPayBillOnline,CustomerBuyGoodsOnline',
            'Amount' => 'required|numeric|min:1',
            'PartyA' => 'required|string',
            'PartyB' => 'required|string',
            'PhoneNumber' => 'required|phone_number',
            'CallBackURL' => 'required|url',
            'AccountReference' => 'required_if:TransactionType,CustomerBuyGoodsOnline|string',
            'TransactionDesc' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        // Http call
        $response = $this->http_client
            ->replaceHeaders(['Authorization' => "Bearer {$this->token}"])
            ->post('/mpesa/stkpush/v1/processrequest', $validator->validated());

        // Return the response
        return $response;
    }

    /**
     * @param string $checkout_request_id The checkout request ID from the STK push
     * @param string $transaction_id The transaction ID from the STK push
     *
     * @return \Illuminate\Http\Client\Response
     *
     * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException If the validation fails
     */
    public function stkPushQuery($checkout_request_id): \Illuminate\Http\Client\Response
    {
        // Generate the token
        $this->generateToken();

        // Timestamp
        $timestamp = date('YmdHis');

        // Data
        $data = [
            'BusinessShortCode' => $this->short_code,
            'Password' => $this->generateStkPassword($timestamp),
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkout_request_id,
        ];

        // Validate the parameters
        $validator = self::validator($data, [
            'BusinessShortCode' => 'required|numeric',
            'Password' => 'required|string',
            'Timestamp' => 'required|date_format:YmdHis',
            'CheckoutRequestID' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        // Http call
        $response = $this->http_client
            ->replaceHeaders(['Authorization' => "Bearer {$this->token}"])
            ->post('/mpesa/stkpushquery/v1/query', $validator->validated());

        // Return the response
        return $response;
    }
}
