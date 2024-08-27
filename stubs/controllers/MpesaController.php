<?php

namespace App\Http\Controllers\LaravelMpesa;

use Ghostscypher\Mpesa\Facades\Mpesa;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Mpesa callback controller
 *
 * This controller handles all the callbacks from Mpesa and logs them to the database
 * you are free to modify this controller to suit your needs
 */
class MpesaController
{
    /**
     * Log the request
     *
     * @param  \Illuminate\Http\Request  $request  The request object
     * @param  \Illuminate\Http\JsonResponse  $response  The response object
     * @param  string  $type  The type of callback
     * @param  string|null  $reference_id  The reference id
     */
    protected static function logRequest($request, $response, string $type, ?string $reference_id = null): void
    {
        // If callback logging is enabled store this in the database
        if (config('mpesa.features.enable_callback_logging')) {
            try {
                $callback_model = app(config('mpesa.models.callback_log'));

                $callback_model::create([
                    'reference_id' => $reference_id,
                    'callback_type' => $type,
                    'endpoint' => $request->fullUrl(),
                    'method' => $request->method(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'request_headers' => $request->headers->all(),
                    'request_body' => $request->all(),
                    'response_status' => $response->status(),
                    'response_headers' => $response->headers->all(),
                    'response_body' => $response->getContent(),
                ]);

                return;
            } catch (\Exception|\Throwable $e) {
                Log::error($e, [
                    'additional_message' => 'Did you migrate the tables? Run php artisan migrate to create the tables, if you have not done so already',
                ]);
            }
        }

        // Log the request to a file if we are not storing in the database or if an error occurred
        Log::info('Received Mpesa callback', [
            'type' => $type,
            'url' => $request->fullUrl(),
            'request' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    public function stkPushCallback(Request $request): JsonResponse
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'stk'));

        // Get checkout id
        $checkout_request_id = $request->input('Body.stkCallback.CheckoutRequestID');

        // Get and extract the callback items
        $callback_items = $request->input('Body.stkCallback.CallbackMetadata.Item', []);
        $items = Mpesa::deconstructData($callback_items, 'Name', 'Value');
        $result_code = $request->input('Body.stkCallback.ResultCode', -1);

        // Log the request
        $response = response()->json([
            'message' => 'Received STK push callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'stk', $checkout_request_id);

        return $response;
    }

    /**
     * Handle the c2b validation request
     *
     * @return void
     *
     * @see https://developer.safaricom.co.ke/APIs/CustomerToBusinessRegisterURL
     */
    public function c2bValidation(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'c2b_validation'));

        // TODO: Perform your validation here
        // Your code here

        // Respond to the request, for success
        $response = response()->json([
            'message' => 'Received C2B validation callback',
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        // For errors respond with an error code i.e. any other code apart from 0
        // Some of the available Error codes
        // C2B00011 - Invalid MSISDN
        // C2B00012 - Invalid Account Number
        // C2B00013 - Invalid Amount
        // C2B00014 - Invalid KYC Details
        // C2B00015 - Invalid Shortcode
        // C2B00016 - Other error

        // $response = response()->json([
        //     'message' => 'Received C2B validation callback',
        //     'ResultCode' => 'C2B00011',
        //     'ResultDesc' => 'Rejected',
        // ]);

        // Transaction id
        $transaction_id = $request->input('TransID');

        // Log the request
        self::logRequest($request, $response, 'c2b_validation', $transaction_id);

        return $response;
    }

    /**
     * Handle the c2b confirmation request
     *
     * @return void
     */
    public function c2bConfirmation(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'c2b_confirmation'));

        // Get the transaction id
        $transaction_id = $request->input('TransID');

        $response = response()->json([
            'message' => 'Received C2B confirmation callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'c2b_confirmation', $transaction_id);

        return $response;
    }

    public function b2cResult(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'b2c_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $parameters = Mpesa::deconstructData($callback_parameters, 'Key', 'Value');
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received B2C result callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'b2c_result', $transaction_id);

        return $response;
    }

    public function b2cTimeout(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'b2c_timeout'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received B2C timeout callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'b2c_timeout', $transaction_id);

        return $response;
    }

    public function b2bResult(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'b2b_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $parameters = Mpesa::deconstructData($callback_parameters, 'Key', 'Value');
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received B2B result callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'b2b_result', $transaction_id);

        return $response;
    }

    public function b2bTimeout(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'b2b_timeout'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received B2B timeout callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'b2b_timeout', $transaction_id);

        return $response;
    }

    public function b2bStkCallback(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'b2b_stk'));

        // Your code here
        $transaction_id = $request->input('transactionId');

        // Get and extract the callback items
        $result_code = $request->input('resultCode', -1);

        $response = response()->json([
            'message' => 'Received B2B STK callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'b2b_stk', $transaction_id);

        return $response;
    }

    public function statusResult(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'status_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $parameters = Mpesa::deconstructData($callback_parameters, 'Key', 'Value');
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received B2B result callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'status_result', $transaction_id);

        return $response;
    }

    public function statusTimeout(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'status_timeout'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received status timeout callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'status_timeout', $transaction_id);

        return $response;
    }

    public function reversalResult(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'reversal_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $parameters = Mpesa::deconstructData($callback_parameters, 'Key', 'Value');
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received reversal result callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'reversal_result', $transaction_id);

        return $response;
    }

    public function reversalTimeout(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'reversal_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received reversal timeout callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'reversal_timeout', $transaction_id);

        return $response;
    }

    public function balanceResult(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'balance_result'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $parameters = Mpesa::deconstructData($callback_parameters, 'Key', 'Value');

        // If isset $parameters['AccountBalance'] then extract the balance data
        // You can use the parseMpesaBalance method to parse the balance data
        if (isset($parameters['AccountBalance'])) {
            $balance_data = Mpesa::parseMpesaBalance($parameters['AccountBalance']);
        }

        $response = response()->json([
            'message' => 'Received balance result callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'balance_result', $transaction_id);

        return $response;
    }

    public function balanceTimeout(Request $request)
    {
        // Dispatch the event
        event(new \Ghostscypher\Mpesa\Events\MpesaCallbackReceived($request, 'balance_timeout'));

        // Your code here
        $transaction_id = $request->input('Result.TransactionID');

        // Get and extract the callback items
        $callback_parameters = $request->input('Result.ResultParameters.ResultParameter', []);
        $result_code = $request->input('Result.ResultCode', -1);

        $response = response()->json([
            'message' => 'Received balance timeout callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        self::logRequest($request, $response, 'balance_timeout');

        return $response;
    }

    /**
     * Handle the bill manager callback, this can be several things
     *
     * @return void
     */
    public function billManagerCallback(Request $request)
    {
        $response = response()->json([
            'message' => 'Received bill manager callback',
            'success' => true,
            'ResultCode' => '0',
            'ResultDesc' => 'Accepted',
        ]);

        // Your code here
        self::logRequest($request, $response, 'bill_manager');

        return $response;
    }
}
