<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

/**
 * Utility APIs i.e. reversal, balance, transaction status, etc
 */
trait MpesaUtility
{
    use MpesaAuth;

    /**
     * Check the transaction status
     *
     * @param  string  $transaction_id  The unique transaction ID returned by the M-Pesa
     * @param  string  $identifier_type  Type of organization receiving the transaction 1 – MSISDN 2 – Till Number 4 – Organization shortcode
     * @param  string  $result_url  The url to which the results will be sent by M-Pesa
     * @param  string  $queue_timeout_url  The url to which timeout will be sent by M-Pesa
     * @param  string  $shortcode  The organization shortcode used to receive the transaction
     * @param  string  $remarks  Comments that are sent along with the transaction
     * @param  string  $occassion  The reason for the transaction
     * @param  string  $initiator_name  The name of the initiator
     * @param  string  $initiator_password  The password for the initiator, this will be used to generate the security credential
     */
    public function transactionStatus(
        string $transaction_id,
        string $identifier_type = '4',
        ?string $result_url = null,
        ?string $queue_timeout_url = null,
        ?string $shortcode = null,
        string $remarks = 'Remarks',
        string $occassion = 'Occassion',
        ?string $initiator_name = null,
        ?string $initiator_password = null
    ): Response {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'Initiator' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $transaction_id,
            'OriginatorConversationID' => $this->generateOriginatorConversationId('TransactionStatusQuery'),
            'PartyA' => $shortcode ?? config('mpesa.shortcode'),
            'IdentifierType' => $identifier_type,
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.status_result_url')),
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.status_timeout_url')),
            'Remarks' => $remarks,
            'Occassion' => $occassion,
        ], [
            'Initiator' => 'required|string',
            'SecurityCredential' => 'required|string',
            'CommandID' => 'required|string|in:TransactionStatusQuery',
            'TransactionID' => 'required|string',
            'OriginatorConversationID' => 'required|string',
            'PartyA' => 'required|string',
            'IdentifierType' => 'required|string',
            'ResultURL' => 'required|url',
            'QueueTimeOutURL' => 'required|url',
            'Remarks' => 'required|string',
            'Occassion' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        $response = $this->http_client->post('mpesa/transactionstatus/v1/query', $validator->validated());

        return $response;
    }

    /**
     * Check the balance of the organization
     *
     * @param  string  $result_url  The url to which the results will be sent by M-Pesa
     * @param  string  $queue_timeout_url  The url to which timeout will be sent by M-Pesa
     * @param  string  $shortcode  The organization shortcode used to receive the transaction
     * @param  string  $identifier_type  Type of organization receiving the transaction 2 – Till Number 4 – Organization shortcode
     * @param  string  $remarks  Comments that are sent along with the transaction
     * @param  string  $occassion  The reason for the transaction
     * @param  string  $initiator_name  The name of the initiator
     * @param  string  $initiator_password  The password for the initiator, this will be used to generate the security credential
     */
    public function checkBalance(
        ?string $result_url = null,
        ?string $queue_timeout_url = null,
        ?string $shortcode = null,
        string $identifier_type = '4',
        string $remarks = 'Remarks',
        string $occassion = 'Occassion',
        ?string $initiator_name = null,
        ?string $initiator_password = null
    ): Response {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'Initiator' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => 'AccountBalance',
            'PartyA' => $shortcode ?? config('mpesa.shortcode'),
            'IdentifierType' => $identifier_type,
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.balance_result_url')),
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.balance_timeout_url')),
            'Remarks' => $remarks,
            'Occassion' => $occassion,
        ], [
            'Initiator' => 'required|string',
            'SecurityCredential' => 'required|string',
            'CommandID' => 'required|string|in:AccountBalance',
            'PartyA' => 'required|string',
            'IdentifierType' => 'required|string',
            'ResultURL' => 'required|url',
            'QueueTimeOutURL' => 'required|url',
            'Remarks' => 'required|string',
            'Occassion' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/transactionstatus/v1/query', $validator->validated());
    }

    /**
     * Reverses a transaction
     *
     * @param  string  $transaction_id  The unique transaction ID returned by the M-Pesa
     * @param  string  $amount  The amount to reverse
     * @param  string  $result_url  The url to which the results will be sent by M-Pesa
     * @param  string  $queue_timeout_url  The url to which timeout will be sent by M-Pesa
     * @param  string  $reciever_identifier_type  Type of organization receiving the transaction default is 11
     * @param  string  $shortcode  The organization shortcode used to receive the transaction
     * @param  string  $remarks  Comments that are sent along with the transaction
     * @param  string  $occassion  The reason for the transaction
     * @param  string  $initiator_name  The name of the initiator
     * @param  string  $initiator_password  The password for the initiator, this will be used to generate the security credential
     */
    public function reverseTransaction(
        string $transaction_id,
        string $amount,
        ?string $result_url = null,
        ?string $queue_timeout_url = null,
        string $reciever_identifier_type = '11',
        ?string $shortcode = null,
        string $remarks = 'Remarks',
        string $occassion = 'Occassion',
        ?string $initiator_name = null,
        ?string $initiator_password = null
    ): Response {
        // Generate token
        $this->generateToken();

        $validator = self::validate([
            'Initiator' => $initiator_name ?? config('mpesa.initiator_name'),
            'SecurityCredential' => $this->generateSecurityCredential($initiator_password),
            'CommandID' => 'TransactionStatusQuery',
            'TransactionID' => $transaction_id,
            'Amount' => $amount,
            'OriginatorConversationID' => $this->generateOriginatorConversationId('TransactionStatusQuery'),
            'ReceiverParty' => $shortcode ?? config('mpesa.shortcode'),
            'ReceiverIdentifierType' => $reciever_identifier_type,
            'ResultURL' => $result_url ?? self::generateCallbackUrl(config('mpesa.status_result_url')),
            'QueueTimeOutURL' => $queue_timeout_url ?? self::generateCallbackUrl(config('mpesa.status_timeout_url')),
            'Remarks' => $remarks,
            'Occassion' => $occassion,
        ], [
            'Initiator' => 'required|string',
            'SecurityCredential' => 'required|string',
            'CommandID' => 'required|string|in:TransactionStatusQuery',
            'TransactionID' => 'required|string',
            'OriginatorConversationID' => 'required|string',
            'ReceiverParty' => 'required|string',
            'ReceiverIdentifierType' => 'required|string',
            'ResultURL' => 'required|url',
            'QueueTimeOutURL' => 'required|url',
            'Remarks' => 'required|string',
            'Occassion' => 'required|string',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('mpesa/reversal/v1/request', $validator->validated());
    }
}
