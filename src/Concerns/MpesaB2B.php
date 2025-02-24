<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

trait MpesaB2B
{
    use MpesaAuth;

    /**
     * Allows businesses to pay individuals directly from their bulk payment account to their M-Pesa accounts
     *
     * @param  string  $receiving_shortcode  the shortcode receiving the payment
     * @param  string  $amount  the amount to be sent
     * @param  string  $account_reference  the account number/reference for the transaction
     * @param  string  $queue_timeout_url  the URL to receive a timeout response
     * @param  string  $result_url  the URL to receive the result of the transaction
     * @param  string  $command_id  the command ID for the transaction
     *                              (BusinessPayBill, BusinessBuyGoods, BusinessPayToBulk, DisburseFundsToBusiness, BusinessToBusinessTransfer, MerchantToMerchantTransfer) - Default is BusinessPayBill
     * @param  string  $remarks  the remarks for the transaction
     * @param  string  $requester_phone_number  Optional. The consumer’s mobile number on behalf of whom you are paying.
     * @param  string  $originator_conversation_id  the unique ID for the transaction
     * @param  string  $shortcode  the shortcode to use for the transaction
     * @param  string  $initiator_name  the name of the initiator
     * @param  string  $initiator_password  the password for the initiator - This will be used to generate the security credential
     * @param  string  $sender_identifier_type  the type of the sender identifier, default is 4, we don't recommend changing this but have left it as a parameter in case you need to change it
     * @param  string  $reciever_identifier_type  the type of the receiver identifier, default is 4, we don't recommend changing this but have left it as a parameter in case you need to change it
     */
    public function B2B(
        string $receiving_shortcode,
        string $amount,
        string $account_reference = 'Account Reference',
        ?string $queue_timeout_url = null,
        ?string $result_url = null,
        string $command_id = 'BusinessPayBill',
        string $remarks = 'Remarks',
        ?string $requester_phone_number = null,
        ?string $originator_conversation_id = null,
        ?string $shortcode = null,
        ?string $initiator_name = null,
        ?string $initiator_password = null,
        string $sender_identifier_type = '4',
        string $reciever_identifier_type = '4'
    ): Response {
        // Generate token
        $this->generateToken();

        if ($requester_phone_number) {
            $requester_phone_number = self::formatPhoneNumberE164($requester_phone_number);
        }

        $validator = self::validate([
            'OriginatorConversationID' => $originator_conversation_id ?? $this->generateOriginatorConversationId('B2B_'),
            'Initiator' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => $command_id,
            'SenderIdentifierType' => $sender_identifier_type,
            'RecieverIdentifierType' => $reciever_identifier_type,
            'Amount' => $amount,
            'PartyA' => $shortcode ?? config('mpesa.shortcode'),
            'PartyB' => $receiving_shortcode,
            'AccountReference' => $account_reference,
            'Requester' => $requester_phone_number,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.b2b_timeout_url')),
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.b2b_result_url')),
        ], [
            'OriginatorConversationID' => 'required',
            'Initiator' => 'required',
            'SecurityCredential' => 'required',
            'CommandID' => 'required|in:BusinessPayBill,BusinessBuyGoods,BusinessPayToBulk,DisburseFundsToBusiness,BusinessToBusinessTransfer,MerchantToMerchantTransfer',
            'SenderIdentifierType' => 'required',
            'RecieverIdentifierType' => 'required',
            'Amount' => 'required|numeric|min:0',
            'PartyA' => 'required',
            'PartyB' => 'required',
            'AccountReference' => 'required',
            'Requester' => 'nullable|phone_number',
            'Remarks' => 'required',
            'QueueTimeOutURL' => 'required|url',
            'ResultURL' => 'required|url',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        $data = $validator->validated();

        // Remove the Requester key if it is not set
        if (! $requester_phone_number) {
            unset($data['Requester']);
        }

        return $this->http_client->post('mpesa/b2b/v1/paymentrequest', $data);
    }

    /**
     * Allows businesses to pay taxes to KRA, since this is a rarely used API, it does not have
     * a management feature, if you need to use this API, you will have to manage the responses
     * yourself, i.e. the queue_timeout_url and result_url
     *
     * @param  string  $receiving_shortcode  the shortcode receiving the payment
     * @param  string  $amount  the amount to be sent
     * @param  string  $account_reference  - The payment registration number (PRN) issued by KRA e.g. PRN1234XN.
     * @param  string  $queue_timeout_url  the URL to receive a timeout response
     * @param  string  $result_url  the URL to receive the result of the transaction
     * @param  string  $remarks  the remarks for the transaction
     * @param  string  $requester_phone_number  Optional. The consumer’s mobile number on behalf of whom you are paying.
     * @param  string  $originator_conversation_id  the unique ID for the transaction
     * @param  string  $shortcode  the shortcode to use for the transaction
     * @param  string  $initiator_name  the name of the initiator
     * @param  string  $initiator_password  the password for the initiator - This will be used to generate the security credential
     * @param  string  $party_b  The shortcode of the organization receiving the payment. Default is 572572, this is the KRA shortcode,
     *                           we don't recommend changing this but have left it as a parameter in case you need to change it
     * @param  string  $sender_identifier_type  the type of the sender identifier, default is 4, we don't recommend changing this but have left it as a parameter in case you need to change it
     * @param  string  $reciever_identifier_type  the type of the receiver identifier, default is 4, we don't recommend changing this but have left it as a parameter in case you need to change it
     */
    public function B2BRemitTax(
        string $amount,
        string $account_reference,
        ?string $queue_timeout_url = null,
        ?string $result_url = null,
        string $remarks = 'Remarks',
        ?string $requester_phone_number = null,
        ?string $originator_conversation_id = null,
        ?string $shortcode = null,
        ?string $initiator_name = null,
        ?string $initiator_password = null,
        string $party_b = '572572',
        string $sender_identifier_type = '4',
        string $reciever_identifier_type = '4'
    ): Response {
        // Generate token
        $this->generateToken();

        if ($requester_phone_number) {
            $requester_phone_number = self::formatPhoneNumberE164($requester_phone_number);
        }

        $validator = self::validate([
            'OriginatorConversationID' => $originator_conversation_id ?? $this->generateOriginatorConversationId('B2B_'),
            'Initiator' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => 'PayTaxToKRA',
            'SenderIdentifierType' => $sender_identifier_type,
            'RecieverIdentifierType' => $reciever_identifier_type,
            'Amount' => $amount,
            'PartyA' => $shortcode ?? config('mpesa.shortcode'),
            'PartyB' => $party_b,
            'AccountReference' => $account_reference,
            'Requester' => $requester_phone_number,
            'Remarks' => $remarks,
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.b2b_timeout_url')),
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.b2b_result_url')),
        ], [
            'OriginatorConversationID' => 'required',
            'Initiator' => 'required',
            'SecurityCredential' => 'required',
            'CommandID' => 'required|in:PayTaxToKRA',
            'SenderIdentifierType' => 'required|in:4',
            'RecieverIdentifierType' => 'required|in:4',
            'Amount' => 'required|numeric|min:0',
            'PartyA' => 'required',
            'PartyB' => 'required',
            'AccountReference' => 'required',
            'Requester' => 'nullable|phone_number',
            'Remarks' => 'required',
            'QueueTimeOutURL' => 'required|url',
            'ResultURL' => 'required|url',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        $data = $validator->validated();

        // Remove the Requester key if it is not set
        if (! $requester_phone_number) {
            unset($data['Requester']);
        }

        return $this->http_client->post('mpesa/b2b/v1/paymentrequest', $data);
    }

    /**
     * Initiate a B2B STK push
     *
     * @param  string  $reciever_shortcode  - The shortcode receiving the payment
     * @param  int  $amount  - The amount to be sent
     * @param  string  $account_reference  - The account number/reference for the transaction
     * @param  string  $callback_url  - The URL to receive the callback response
     * @param  string  $partner_name  - This is the organization Friendly name used by the vendor as known by the Merchant.
     * @param  string  $request_reference_id  - Unique identifier for the transaction request. Maximum of 12 characters.
     */
    public function B2BStkPush(
        string $reciever_shortcode,
        string $amount,
        string $account_reference = 'Account Reference',
        ?string $callback_url = null,
        ?string $shortcode = null,
        ?string $partner_name = null,
        ?string $request_reference_id = null
    ): Response {
        // Generate token
        $this->generateToken();

        // If amount does not have a decimal point, add it
        if (! strpos($amount, '.')) {
            $amount = "{$amount}.00";
        }

        $validator = self::validate([
            'primaryShortCode' => $shortcode ?? config('mpesa.shortcode'),
            'receiverShortCode' => $reciever_shortcode,
            'amount' => $amount,
            'paymentRef' => $account_reference,
            'callbackUrl' => $callback_url ?? self::generateCallbackUrl(config('mpesa.b2b_stk_callback_url')),
            'partnerName' => $partner_name ?? config('mpesa.partner_name') ?? config('mpesa.initiator_name'),
            'RequestID' => $request_reference_id ?? $this->generateOriginatorConversationId('B2B_STK_'),
        ], [
            'primaryShortCode' => 'required',
            'receiverShortCode' => 'required',
            'amount' => 'required|numeric|min:0',
            'paymentRef' => 'required',
            'callbackUrl' => 'required|url',
            'partnerName' => 'required',
            'RequestID' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('v1/ussdpush/get-msisdn', $validator->validated());

    }
}
