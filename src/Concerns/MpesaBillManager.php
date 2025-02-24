<?php

namespace Ghostscypher\Mpesa\Concerns;

use Ghostscypher\Mpesa\Exceptions\MpesaValidationException;
use Illuminate\Http\Client\Response;

/**
 * Mpesa Daraja Bill management APIs
 *
 * Note: Since this API is rarely used it's upon the user to manage the callbacks associated with the bill manager,
 * In the future, I might add a feature to manage the callbacks
 *
 * @see https://developer.safaricom.co.ke/APIs/BillManager
 */
trait MpesaBillManager
{
    use MpesaAuth;

    /**
     * This is the initial API call to the bill manager where you opt in to the service
     *
     * @param  string  $email  - This is the official contact email address for the organization signing up to bill manager.
     *                         It will appear in features sent to the customer such as invoices and payment receipts for customers
     *                         to reach out to you as a business.
     * @param  string  $phone_number  - This is the official contact phone number for the organization signing up to bill manager.
     * @param  string  $callback_url  - This is the URL that will be used to send payment notifications to your system.
     * @param  bool  $send_reminders  - This field gives you the flexibility as a business to enable or disable sms payment reminders for invoices sent.
     *                                A payment reminder is sent 7 days before the due date, 3 days before the due date and on the day the payment is due.
     *                                The default value is false.
     * @param  string  $logo_url  - This is the URL to the logo of the organization, the image will be included in the invoice sent to the customer.
     * @param  string  $short_code  - This is the short code that will be used to send payment notifications to your system.
     */
    public function billManagerOptIn(
        string $email,
        string $phone_number,
        ?string $callback_url = null,
        bool $enable_reminders = false,
        ?string $logo_url = null,
        ?string $short_code = null
    ): Response {
        $this->generateToken();

        // Convert phone
        $phone_number = self::formatPhoneNumberE164($phone_number);

        $validator = self::validate([
            'shortcode' => $short_code ?? config('mpesa.shortcode'),
            'email' => $email,
            'officialContact' => $phone_number,
            'callbackurl' => $callback_url ?? self::generateCallbackUrl(config('mpesa.bill_manager_callback_url')),
            'sendReminders' => $enable_reminders ? 1 : 0,
            'logo' => $logo_url,
        ], [
            'email' => 'required|email',
            'officialContact' => 'required|phone_number',
            'callbackurl' => 'required|url',
            'sendReminders' => 'integer|in:0,1',
            'logo' => 'nullable|url',
            'shortcode' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        // Data
        $data = $validator->validated();

        // Remove log if null
        if (! $logo_url) {
            unset($data['logo']);
        }

        // https://api.safaricom.co.ke/v1/billmanager-invoice/optin
        $response = $this->http_client->post('/v1/billmanager-invoice/optin', $data);

        return $response;
    }

    /**
     * Update opt in details
     *
     * @param  string  $email  - This is the official contact email address for the organization signing up to bill manager.
     *                         It will appear in features sent to the customer such as invoices and payment receipts for customers
     *                         to reach out to you as a business.
     * @param  string  $phone_number  - This is the official contact phone number for the organization signing up to bill manager.
     * @param  string  $callback_url  - This is the URL that will be used to send payment notifications to your system.
     * @param  bool  $send_reminders  - This field gives you the flexibility as a business to enable or disable sms payment reminders for invoices sent.
     *                                A payment reminder is sent 7 days before the due date, 3 days before the due date and on the day the payment is due.
     *                                The default value is false.
     * @param  string  $logo_url  - This is the URL to the logo of the organization, the image will be included in the invoice sent to the customer.
     * @param  string  $short_code  - This is the short code that will be used to send payment notifications to your system.
     */
    public function billManagerUpdateDetails(
        string $email,
        string $phone_number,
        ?string $callback_url = null,
        bool $enable_reminders = false,
        ?string $logo_url = null,
        ?string $short_code = null
    ): Response {
        $this->generateToken();

        // Convert phone
        $phone_number = self::formatPhoneNumberE164($phone_number);

        $validator = self::validate([
            'shortcode' => $short_code ?? config('mpesa.shortcode'),
            'email' => $email,
            'officialContact' => $phone_number,
            'callbackurl' => $callback_url ?? self::generateCallbackUrl(config('mpesa.bill_manager_callback_url')),
            'sendReminders' => $enable_reminders ? 1 : 0,
            'logo' => $logo_url,
        ], [
            'email' => 'required|email',
            'officialContact' => 'required|phone_number',
            'callbackurl' => 'required|url',
            'sendReminders' => 'integer|in:0,1',
            'logo' => 'nullable|url',
            'shortcode' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        // Data
        $data = $validator->validated();

        // Remove log if null
        if (! $logo_url) {
            unset($data['logo']);
        }

        return $this->http_client->post('/v1/billmanager-invoice/change-optin-details', $data);

    }

    /**
     * Bill Manager invoicing service enables you to create and send e-invoices to your customers.
     * Single invoicing functionality will allow you to send out customized individual e-invoices.
     * Your customers will receive this notification(s) via an SMS to the Safaricom phone number
     * specified while creating the invoice.
     *
     * @param  array  $data  - The data to send to the API in thr format below
     *                       ```json
     *
     *{
     *     "externalReference": "#9932340",
     *     "billedFullName": "John Doe",
     *       "billedPhoneNumber": "07XXXXXXXX",
     *       "billedPeriod": "August 2021",
     *       "invoiceName": "Jentrys",
     *       "dueDate": "2021-10-12",
     *       "accountReference": "1ASD678H",
     *       "amount": "800",
     *       "invoiceItems": [
     *           {
     *           "itemName": "food",
     *          "amount": "700"
     *           },
     *           {
     *          "itemName": "water",
     *          "amount": "100"
     *           }
     *       ]
     *   }
     * ```
     */
    public function billManagerSingleInvoicing(array $data): Response
    {
        // Generate token
        $this->generateToken();

        $validator = self::validate($data, [
            'externalReference' => 'required',
            'billedFullName' => 'required',
            'billedPhoneNumber' => 'required|phone_number_lax',
            'billedPeriod' => 'required',
            'invoiceName' => 'required',
            'dueDate' => 'required|date',
            'accountReference' => 'required',
            'amount' => 'required|numeric',
            'invoiceItems' => 'required|array',
            'invoiceItems.*.itemName' => 'required',
            'invoiceItems.*.amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('/v1/billmanager-invoice/single-invoicing', $validator->validated());
    }

    /**
     * Bill Manager invoicing service enables you to create and send e-invoices to your customers.
     * Single invoicing functionality will allow you to send out customized individual e-invoices.
     * Your customers will receive this notification(s) via an SMS to the Safaricom phone number
     * specified while creating the invoice.
     *
     * @param  array  $data  - The data to send to the API in thr format below
     *                       ```json
     *
     *[
     * {
     *     "externalReference": "#9932340",
     *     "billedFullName": "John Doe",
     *       "billedPhoneNumber": "07XXXXXXXX",
     *       "billedPeriod": "August 2021",
     *       "invoiceName": "Jentrys",
     *       "dueDate": "2021-10-12",
     *       "accountReference": "1ASD678H",
     *       "amount": "800",
     *       "invoiceItems": [
     *           {
     *           "itemName": "food",
     *          "amount": "700"
     *           },
     *           {
     *          "itemName": "water",
     *          "amount": "100"
     *           }
     *       ]
     *   },
     *  {
     *     "externalReference": "#9932340",
     *     "billedFullName": "John Doe",
     *       "billedPhoneNumber": "07XXXXXXXX",
     *       "billedPeriod": "August 2021",
     *       "invoiceName": "Jentrys",
     *       "dueDate": "2021-10-12",
     *       "accountReference": "1ASD678H",
     *       "amount": "800",
     *       "invoiceItems": [
     *           {
     *           "itemName": "food",
     *          "amount": "700"
     *           },
     *           {
     *          "itemName": "water",
     *          "amount": "100"
     *           }
     *       ]
     *   }
     * ]
     * ```
     */
    public function billManagerBulkInvoicing(array $data): Response
    {
        // Generate token
        $this->generateToken();

        $validator = self::validate($data, [
            '*.externalReference' => 'required',
            '*.billedFullName' => 'required',
            '*.billedPhoneNumber' => 'required|phone_number_lax',
            '*.billedPeriod' => 'required',
            '*.invoiceName' => 'required',
            '*.dueDate' => 'required|date',
            '*.accountReference' => 'required',
            '*.amount' => 'required|numeric',
            '*.invoiceItems' => 'required|array',
            '*.invoiceItems.*.itemName' => 'required',
            '*.invoiceItems.*.amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('/v1/billmanager-invoice/bulk-invoicing', $validator->validated());
    }

    /**
     * This API call will allow you to reconcile the invoices that have been sent to your customers.
     * Please note that you will be required to handle the callbacks associated with the bill manager.
     *
     * @param  string  $transaction_id  - Mpesa transaction ID
     * @param  int  $paid_amount  - The amount paid by the customer
     * @param  string  $msisdn  - The phone number of the customer
     * @param  string  $date_created  - The date the transaction was created
     * @param  string  $account_reference  - The account reference of the transaction
     * @param  string  $short_code  - The short code to use, if not provided the default short code will be used
     */
    public function billManagerReconciliation(
        string $transaction_id,
        int $paid_amount,
        string $msisdn,
        string $date_created,
        string $account_reference,
        ?string $short_code = null
    ): Response {
        // Generate token
        $this->generateToken();

        // Convert phone
        $msisdn = self::formatPhoneNumberE164($msisdn);

        $validator = self::validate([
            'shortcode' => $short_code ?? config('mpesa.shortcode'),
            'transactionId' => $transaction_id,
            'paidAmount' => $paid_amount,
            'msisdn' => $msisdn,
            'dateCreated' => $date_created,
            'accountReference' => $account_reference,
        ], [
            'shortcode' => 'required',
            'transactionId' => 'required',
            'paidAmount' => 'required|numeric',
            'msisdn' => 'required|phone_number',
            'dateCreated' => 'required|date',
            'accountReference' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('/v1/billmanager-invoice/reconciliation', $validator->validated());
    }

    /**
     * This API call will allow you to cancel a single invoice that has been sent to your customers.
     *
     * @param  array  $data  - The data to send to the API in the format below
     *                       ```json
     *                       {
     *                       "externalReference": "1134",
     *                       }
     *                       ```
     */
    public function billManagerCancelSingleInvoicing(array $data): Response
    {
        // Generate token
        $this->generateToken();

        $validator = self::validate($data, [
            'externalReference' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('/v1/billmanager-invoice/cancel-single-invoice', $validator->validated());
    }

    /**
     * This API call will allow you to cancel a single invoice that has been sent to your customers.
     *
     * @param  array  $data  - The data to send to the API in the format below
     *                       ```json
     *                       [
     *                       {
     *                       "externalReference": "1134",
     *                       },
     *                       {
     *                       "externalReference": "1135",
     *                       }
     *                       ]
     *                       ```
     */
    public function billManagerCancelBulkInvoicing(array $data): Response
    {
        // Generate token
        $this->generateToken();

        $validator = self::validate($data, [
            '*.externalReference' => 'required',
        ]);

        if ($validator->fails()) {
            throw new MpesaValidationException($validator->errors()->toArray());
        }

        return $this->http_client->post('/v1/billmanager-invoice/cancel-bulk-invoice', $validator->validated());
    }
}
