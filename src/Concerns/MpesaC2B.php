<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

trait MpesaC2B
{
    use MpesaAuth;

    /**
     * Summary of registerUrl
     *
     * @param  string  $validdation_url  - The URL to receive validation request from Safaricom, note that validation needs to be enabled for this endpoint to be called
     * @param  string  $confirmation_url  - Once payment is successful this endpoint will be called
     * @param  string  $response_type  - Incase validation endpoint is not reachable we use this to determine whether to complete or cancel the transaction (Canceled, Completed)
     * @param  string  $shortcode  - The short code to be used for the transaction
     */
    public function registerUrl(
        ?string $validdation_url = null,
        ?string $confirmation_url = null,
        string $response_type = 'Completed',
        ?string $shortcode = null
    ): Response {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'validation_url' => $validdation_url ?? self::generateCallbackUrl(config('mpesa.c2b_validation_url')),
            'confirmation_url' => $confirmation_url ?? self::generateCallbackUrl(config('mpesa.c2b_confirmation_url')),
            'response_type' => $response_type,
            'shortcode' => $shortcode ?? config('mpesa.shortcode'),
        ], [
            'validation_url' => 'required|url',
            'confirmation_url' => 'required|url',
            'response_type' => 'required|in:Completed,Canceled',
            'shortcode' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/c2b/register', $validator->validated());
    }

    /**
     * This API is intended for businesses who wish to integrate with standing orders for the automation of recurring revenue collection.
     *
     * @param  string  $standing_order_name  - The name of the standing order
     * @param  string  $phone_number  - The phone number of the customer
     * @param  string  $amount  - The amount to be deducted from the customer
     * @param  string  $start_date  - The date when the standing order should start
     * @param  string  $end_date  - The date when the standing order should end
     * @param  string  $frequency  - The frequency of the standing order
     *                               - 1 - One Off
     *                               - 2 - Daily
     *                               - 3 - Weekly
     *                               - 4 - Monthly
     *                               - 5 - Bi-Monthly
     *                               - 6 - Quarterly
     *                               - 7 - Half Year
     *                               - 8 - Yearly
     * @param  string  $account_reference  - The account reference for the transaction
     * @param  string  $callback_url  - The URL to receive the result of the transaction
     * @param  string  $description  - The description of the transaction
     * @param  string  $business_short_code  - The business short code to use for the transaction
     * @param  string  $transaction_type  - The transaction type to use for the transaction
     *                                    - Standing Order Customer Pay Bill (Default)
     *                                    - Standing Order Customer Pay Marchant
     *
     * @link https://developer.safaricom.co.ke/APIs/MpesaRatiba
     */
    public function ratiba(
        string $standing_order_name,
        string $phone_number,
        string $amount,
        string $start_date,
        string $end_date,
        string $frequency,
        string $account_reference = "Account",
        ?string $callback_url = null,
        string $description = "Payment",
        ?string $business_short_code = null,
        string $transaction_type = "Standing Order Customer Pay Bill",
        string $reciever_identifier_type = "4"
    ): Response
    {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'StandingOrderName' => $standing_order_name,
            'StartDate' => $start_date,
            'EndDate' => $end_date,
            'BusinessShortCode' => $business_short_code ?? config('mpesa.shortcode'),
            'TransactionType' => $transaction_type,
            'ReceiverPartyIdentifierType' => $reciever_identifier_type,
            'ReceiverPartyIdentifier' => $phone_number,
            'Amount' => $amount,
            'PartyA' => self::formatPhoneNumberE164($phone_number),
            'CallBackURL' => $callback_url ?? self::generateCallbackUrl(config('mpesa.ratiba_callback_url')),
            'AccountReference' => $account_reference,
            'TransactionDesc' => $description,
            'Frequency' => $frequency,
        ], [
            'StandingOrderName' => 'required|string',
            'StartDate' => 'required|date:YMD',
            'EndDate' => 'required|date:YMD|after_or_equal:StartDate',
            'BusinessShortCode' => 'required|string',
            'TransactionType' => 'required|string',
            'ReceiverPartyIdentifierType' => 'required|string',
            'ReceiverPartyIdentifier' => 'required|string',
            'Amount' => 'required|numeric|min:0',
            'PartyA' => 'required|phone_number',
            'CallBackURL' => 'required|url',
            'AccountReference' => 'required|string',
            'TransactionDesc' => 'required|string',
            'Frequency' => 'required|in:1,2,3,4,5,6,7,8',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('standingorder/v1/createStandingOrderExternal', $validator->validated());
    }

}
