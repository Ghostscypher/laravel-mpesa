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
     * Logs the STK push response to the database
     *
     * @param \Illuminate\Http\Client\Response $response The response from the STK push request
     * @param array $data The data sent to the STK push request
     * @return void

     */
    protected function logStkPushResponse($response, $data)
    {
        if(! config('mpesa.features.process_stk_push')) {
            return;
        }

        // Log the response to the database
        $response_data = $response->json();

        if($response->status() != 200) {
            $response_data = [
                'MerchantRequestID' => null,
                'CheckoutRequestID' => uniqid('failed-stk-push-', true),
                'ResponseCode' => -1,
                'ResponseDescription' => 'Failed to send STK push',
                'CustomerMessage' => 'Failed to send STK push',
            ];
        }

        /**
         * @var \Ghostscypher\Mpesa\Models\MpesaStkPush $stk_push_model
         */
        $stk_push_model = app(config('mpesa.models.stk_push'));

        // Log the response
        $stk_push_model->updateOrCreate([
            'checkout_request_id' => $response_data['CheckoutRequestID'],
        ], [
            'phone_number' => $data['PhoneNumber'],
            'amount' => $data['Amount'],
            'account_reference' => $data['AccountReference'],
            'original_request' => $data,
            'merchant_request_id' => $response_data['MerchantRequestID'],
            'response_code' => $response_data['ResponseCode'],
            'request_response' => $response_data,
            'status' => $response_data['ResponseCode'] == 0 ? 'pending' : 'failed',
        ]);
    }
    
    /**
     * Logs the STK push Query response to the database, this will be used to track the status of the STK push
     * previously logged from the STK push response
     *
     * @param \Illuminate\Http\Client\Response $response The response from the STK push request
     * @param array $data The data sent to the STK push request
     * @return void

     */
    protected function logStkPushQueryResponse($response, $data)
    {
        if(! config('mpesa.features.process_stk_push')) {
            return;
        }

        // Log the response to the database
        $response_data = $response->json();

        if($response->status() != 200) {
            $response_data = [
                'MerchantRequestID' => null,
                'CheckoutRequestID' => uniqid('failed-stk-push-query-', true),
                'ResultCode' => -1,
                'ResponseDescription' => 'Failed to query STK push',
                'CustomerMessage' => 'Failed to query STK push',
            ];
        }

        /**
         * @var \Ghostscypher\Mpesa\Models\MpesaStkPush $stk_push_model
         */
        $stk_push_model = app(config('mpesa.models.stk_push'));

        // Log the response
        $stk_push_model->updateOrCreate([
            'checkout_request_id' => $data['CheckoutRequestID'],
        ], [
            'result_code' => $response_data['ResultCode'],
            'status' => $response_data['ResultCode'] == 0 ? 'success' : 'failed', // Will also be updated from callback
        ]);
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
        $validator = self::validate($data, [
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
            ->post('/mpesa/stkpush/v1/processrequest', $validator->validated());

        // Log this into a table
        $this->logStkPushResponse($response, $data);

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
        $validator = self::validate($data, [
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
            ->post('/mpesa/stkpushquery/v1/query', $validator->validated());

        // Log this into a table
        $this->logStkPushQueryResponse($response, $data);

        // Return the response
        return $response;
    }
}
