<?php

namespace Ghostscypher\Mpesa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Ghostscypher\Mpesa\Mpesa
 *
 * @method static \Illuminate\Http\Client\Response rawRequest(string $method, string $url, array $data = [], array $headers = [], bool $authenticated = true)
 * @method static \Illuminate\Http\Client\Response B2B(string $receiving_shortcode, string $amount, string $account_reference = "Account Reference", ?string $queue_timeout_url = null, ?string $result_url = null, string $command_id = "BusinessPayBill", string $remarks = "Remarks", ?string $requester_phone_number = null, ?string $originator_conversation_id = null, ?string $shortcode = null, ?string $initiator_name = null, ?string $initiator_password = null, string $sender_identifier_type = "4", string $reciever_identifier_type = "4")
 * @method static \Illuminate\Http\Client\Response B2BRemitTax(string $amount, string $account_reference, ?string $queue_timeout_url = null, ?string $result_url = null, string $remarks = "Remarks", ?string $requester_phone_number = null, ?string $originator_conversation_id = null, ?string $shortcode = null, ?string $initiator_name = null, ?string $initiator_password = null, string $party_b = "572572", string $sender_identifier_type = "4", string $reciever_identifier_type = "4")
 * @method static \Illuminate\Http\Client\Response B2BStkPush(string $reciever_shortcode, string $amount, string $account_reference = "Account Reference", ?string $callback_url = null, ?string $shortcode = null, ?string $partner_name = null, ?string $request_reference_id = null)
 * @method static null|string generateToken(bool $force = false)
 * @method static bool isTokenExpired()
 * @method static string generateSecurityCredential($initiator_password = null)
 * @method static string formatPhoneNumberE164($phone_number)
 * @method static bool isProduction()
 * @method static bool isSandbox()
 * @method static string generateOriginatorConversationId($prefix = "")
 * @method static array parseMpesaBalance(string $balance)
 * @method static \Illuminate\Support\Collection deconstructData($data, $key_name = "Name", $value_key = "Value", $case_insensitive = false)
 * @method static string generateCallbackUrl(string $route)
 * @method static \Illuminate\Http\Client\Response B2C(string $phone_number, string $amount, ?string $queue_timeout_url = null, ?string $result_url = null, string $command_id = "BusinessPayment", string $remarks = "Payment", string $occasion = "Payment", ?string $originator_conversation_id = null, ?string $shortcode = null, ?string $initiator_name = null, ?string $initiator_password = null)
 * @method static \Illuminate\Http\Client\Response billManagerOptIn(string $email, string $phone_number, ?string $callback_url = null, bool $enable_reminders = false, ?string $logo_url = null, ?string $short_code = null)
 * @method static \Illuminate\Http\Client\Response billManagerUpdateDetails(string $email, string $phone_number, ?string $callback_url = null, bool $enable_reminders = false, ?string $logo_url = null, ?string $short_code = null)
 * @method static \Illuminate\Http\Client\Response billManagerSingleInvoicing(array $data)
 * @method static \Illuminate\Http\Client\Response billManagerBulkInvoicing(array $data)
 * @method static \Illuminate\Http\Client\Response billManagerReconciliation(string $transaction_id, int $paid_amount, string $msisdn, string $date_created, string $account_reference, ?string $short_code = null)
 * @method static \Illuminate\Http\Client\Response billManagerCancelSingleInvoicing(array $data)
 * @method static \Illuminate\Http\Client\Response billManagerCancelBulkInvoicing(array $data)
 * @method static \Illuminate\Http\Client\Response registerUrl(?string $validdation_url = null, ?string $confirmation_url = null, string $response_type = "Completed", ?string $shortcode = null)
 * @method static \Illuminate\Http\Client\Response generateQRCode(string $merchant_name, string $ref_no, string $amount, ?string $CPI = null, string $trx_code = "PB", int $size = 300)
 * @method static \Illuminate\Http\Client\Response stkPush(string $phone_number, string $amount, string $account_reference = "Test", ?string $callback_url = null, string $description = "Description", string $transaction_type = "CustomerPayBillOnline", ?string $shortcode = null, ?string $business_short_code = null, ?string $party_a = null)
 * @method static \Illuminate\Http\Client\Response stkPushQuery($checkout_request_id, ?string $shortcode = null)
 * @method static \Illuminate\Http\Client\Response transactionStatus(string $transaction_id, string $identifier_type = "4", ?string $result_url = null, ?string $queue_timeout_url = null, ?string $shortcode = null, string $remarks = "Remarks", string $occassion = "Occassion", ?string $initiator_name = null, ?string $initiator_password = null)
 * @method static \Illuminate\Http\Client\Response checkBalance(?string $result_url = null, ?string $queue_timeout_url = null, ?string $shortcode = null, string $identifier_type = "4", string $remarks = "Remarks", string $occassion = "Occassion", ?string $initiator_name = null, ?string $initiator_password = null)
 * @method static \Illuminate\Http\Client\Response reverseTransaction(string $transaction_id, string $amount, ?string $result_url = null, ?string $queue_timeout_url = null, string $reciever_identifier_type = "11", ?string $shortcode = null, string $remarks = "Remarks", string $occassion = "Occassion", ?string $initiator_name = null, ?string $initiator_password = null)
 */
class Mpesa extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'mpesa';
    }
}
