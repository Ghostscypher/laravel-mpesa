<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

trait MpesaB2C
{
    use MpesaAuth;

    /**
     * Allows businesses to pay individuals directly from their bulk payment account to their M-Pesa accounts
     *
     * @param  string  $phone_number  the phone number receiving the payment
     * @param  string  $amount  the amount to be sent
     * @param  string  $queue_timeout_url  the URL to receive a timeout response
     * @param  string  $result_url  the URL to receive the result of the transaction
     * @param  string  $command_id  the command ID for the transaction (BusinessPayment, SalaryPayment, PromotionPayment)
     * @param  string  $remarks  the remarks for the transaction
     * @param  string  $occasion  the occasion for the transaction
     * @param  string  $originator_conversation_id  the unique ID for the transaction
     * @param  string  $shortcode  the shortcode to use for the transaction
     * @param  string  $initiator_name  the name of the initiator
     * @param  string  $initiator_password  the password for the initiator, this will be used to generate the security credential
     */
    public function B2C(
        string $phone_number,
        string $amount,
        ?string $queue_timeout_url = null,
        ?string $result_url = null,
        string $command_id = 'BusinessPayment',
        string $remarks = 'Payment',
        string $occasion = 'Payment',
        ?string $originator_conversation_id = null,
        ?string $shortcode = null,
        ?string $initiator_name = null,
        ?string $initiator_password = null
    ): Response {
        // Generate token
        $this->generateToken();

        // Convert phone number to the correct format
        $phone_number = self::formatPhoneNumberE164($phone_number);

        $validator = self::validate([
            'OriginatorConversationID' => $originator_conversation_id ?? $this->generateOriginatorConversationId('B2C_'),
            'InitiatorName' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => $command_id,
            'Amount' => $amount,
            'PartyA' => $shortcode ?? config('mpesa.shortcode'),
            'PartyB' => $phone_number,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.b2c_timeout_url')),
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.b2c_result_url')),
            'Occassion' => $occasion,
        ], [
            'OriginatorConversationID' => 'required',
            'InitiatorName' => 'required',
            'SecurityCredential' => 'required',
            'CommandID' => 'required|in:BusinessPayment,SalaryPayment,PromotionPayment',
            'Amount' => 'required|numeric|min:0',
            'PartyA' => 'required',
            'PartyB' => 'required|phone_number',
            'Remarks' => 'required',
            'QueueTimeOutURL' => 'required|url',
            'ResultURL' => 'required|url',
            'Occassion' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/b2c/v3/paymentrequest', $validator->validated());
    }
}
