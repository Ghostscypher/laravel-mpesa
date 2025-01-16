<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;

/**
 * MpesaStkPush Mpesa express (STK Push) API
 */
trait MpesaStkPush
{
    use MpesaAuth;

    /**
     * Generate the STK password
     *
     * @param  string  $shortcode  The shortcode to use for the transaction
     * @param  string  $timestamp  The timestamp to use for the password in the format YmdHis
     */
    protected function generateStkPassword(string $shortcode, string $timestamp): string
    {
        return base64_encode(
            sprintf(
                '%s%s%s',
                $shortcode,
                config('mpesa.passkey') ?? '',
                $timestamp
            )
        );
    }

    /**
     * @param  string  $phone_number  The phone number to send the STK push to (MSISDN)
     * @param  string  $amount  The amount to request from the user, minimum is 1
     * @param  string  $account_reference  The reference number for the transaction, this the account number for the transaction
     * @param  string  $callback_url  The URL to send the callback to, if null we will use the default callback URL
     * @param  string  $description  The description of the transaction
     * @param  string  $transaction_type  The type of transaction, default is CustomerPayBillOnline which is used for paybill numbers, if using a till number use CustomerBuyGoodsOnline
     * @param  string  $shortcode  The shortcode to use for the transaction, if null we will use the default shortcode
     * @param  string  $business_short_code  The business shortcode to use for the transaction, if null we will use the default shortcode
     * @param  string  $party_a  The user phone number, if null we will use the phone number provided
     *
     * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException If the validation fails
     */
    public function stkPush(
        string $phone_number,
        string $amount,
        string $account_reference = 'Test',
        ?string $callback_url = null,
        string $description = 'Description',
        string $transaction_type = 'CustomerPayBillOnline',
        ?string $shortcode = null,
        ?string $business_short_code = null,
        ?string $party_a = null
    ): \Illuminate\Http\Client\Response {
        // Generate the token
        $this->generateToken();

        // Timestamp
        $timestamp = date('YmdHis');
        $phone_number = self::formatPhoneNumberE164($phone_number);

        // Validate the parameters
        $validator = self::validate([
            'BusinessShortCode' => $business_short_code ?? config('mpesa.shortcode'),
            'Password' => $this->generateStkPassword($shortcode ?? config('mpesa.shortcode'), $timestamp),
            'Timestamp' => $timestamp,
            'TransactionType' => $transaction_type,
            'Amount' => $amount,
            'PartyA' => $party_a ?? $phone_number,
            'PartyB' => $shortcode ?? config('mpesa.shortcode'),
            'PhoneNumber' => $phone_number,
            'CallBackURL' => $callback_url ?? self::generateCallbackUrl(config('mpesa.stk_push_callback_url')),
            'AccountReference' => $account_reference,
            'TransactionDesc' => $description,
        ], [
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

        // Return the response
        return $this->http_client->post('/mpesa/stkpush/v1/processrequest', $validator->validated());
    }

    /**
     * @param  string  $checkout_request_id  The checkout request ID from the STK push
     * @param  string  $shortcode  The shortcode to use for the transaction, if null we will use the default shortcode
     *
     * @throws \Ghostscypher\Mpesa\Exceptions\MpesaValidationException If the validation fails
     */
    public function stkPushQuery($checkout_request_id, ?string $shortcode = null): \Illuminate\Http\Client\Response
    {
        // Generate the token
        $this->generateToken();

        // Timestamp
        $timestamp = date('YmdHis');

        // Data
        $data = [
            'BusinessShortCode' => $shortcode ?? config('mpesa.shortcode'),
            'Password' => $this->generateStkPassword($shortcode ?? config('mpesa.shortcode'), $timestamp),
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkout_request_id,
        ];

        // Validate the parameters
        $validator = self::validate($data, [
            'BusinessShortCode' => 'required|numeric',
            'Password' => 'required|string',
            'Timestamp' => 'required|date_format:YmdHis',
            'CheckoutRequestID' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        // Return the response
        return $this->http_client->post('/mpesa/stkpushquery/v1/query', $validator->validated());
    }
}
