<?php

return [

    // Definition of terms
    // B2C - Business to Customer
    // B2B - Business to Business
    // C2B - Customer to Business

    /*******************Start of Mpesa options************************************/
    // Contains options needed when making API calls to the mpesa API
    // Note: not all options are needed for all API calls, some are only needed
    // for specific API calls. Moreover, the methods in the package will have
    // a way to specify the options that are needed for that specific API call.
    // This is just a way to set the default options that will be used when making
    // the API calls.

    /*
    |--------------------------------------------------------------------------
    | Environments
    |--------------------------------------------------------------------------
    |
    | Default service to be used (sandbox or production)
    |
    */
    'env' => env('MPESA_ENV', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Consumer Key
    |--------------------------------------------------------------------------
    |
    | The consumer key provided by safaricom
    |
    */
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Consumer Secret
    |--------------------------------------------------------------------------
    |
    | The consumer secret provided by safaricom
    |
    */
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Passkey
    |--------------------------------------------------------------------------
    |
    | The passkey provided by safaricom for lipa na mpesa online AKA STK push
    |
    */
    'passkey' => env('MPESA_PASSKEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Shortcode
    |--------------------------------------------------------------------------
    |
    | The shortcode provided by safaricom, i.e. the paybill number or till number
    | that will be used to receive payments
    |
    */
    'shortcode' => env('MPESA_SHORTCODE', '174379'),

    /*
    |--------------------------------------------------------------------------
    | Initiator Name
    |--------------------------------------------------------------------------
    |
    | The name of the initiator, usually the shortcode name or the company name
    |
    */
    'initiator_name' => env('MPESA_INITIATOR_NAME', ''),

    /*
    |--------------------------------------------------------------------------
    | Initiator Password
    |--------------------------------------------------------------------------
    |
    | The password of the initiator, usually the shortcode password or the company password
    |
    */
    'initiator_password' => env('MPESA_INITIATOR_PASSWORD', ''),

    /*
    |--------------------------------------------------------------------------
    | B2C Shortcode
    |--------------------------------------------------------------------------
    |
    | The shortcode that will be used to send money to customers
    |
    */
    'b2c_shortcode' => env('MPESA_B2C_SHORTCODE', '600000'),
    
    /*
    |--------------------------------------------------------------------------
    | C2B Validation URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will use to validate the transaction, it will
    | be used to validate the transaction before it is confirmed, this is useful
    | when you want to validate the transaction before it is confirmed e.g.
    | checking if the account number is valid etc.
    |
    | Note: Validation will need to be enabled on the mpesa admin portal, contact
    | customer support to help in enabling it.
    |
    */
    'c2b_validation_url' => env('MPESA_C2B_VALIDATION_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | C2B Confirmation URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction is confirmed
    | and the user has already paid.
    |
    */
    'c2b_confirmation_url' => env('MPESA_C2B_CONFIRMATION_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | B2C Result URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction is complete
    | for a B2C transaction
    |
    */
    'b2c_result_url' => env('MPESA_B2C_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | B2B Result URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction is complete
    | for a B2B transaction
    |
    */
    'b2b_result_url' => env('MPESA_B2B_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | B2B Timeout URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction times out
    | for a B2B transaction
    |
    */
    'b2b_timeout_url' => env('MPESA_B2B_TIMEOUT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | STK Push Result URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction is complete
    | for a STK push transaction / lipa na mpesa online transaction
    |
    */
    'stk_push_callback_url' => env('MPESA_STK_PUSH_CALLBACK_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Status Result URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when you request for the status
    | of a transaction, useful for checking the status of a transaction in the
    | case confirmation was not received.
    |
    */
    'status_result_url' => env('MPESA_STATUS_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Status Timeout URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the transaction status request
    | times out, useful for handling the timeout of a transaction status request
    |
    */
    'status_timeout_url' => env('MPESA_STATUS_TIMEOUT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Reversal Result URL
    |--------------------------------------------------------------------------
    | This is the URL that safaricom will call when you request for a reversal
    | of a transaction.
    |
    */
    'reversal_result_url' => env('MPESA_REVERSAL_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Reversal Timeout URL
    |--------------------------------------------------------------------------
    | This is the URL that safaricom will call when the reversal request times out
    |
    */
    'reversal_timeout_url' => env('MPESA_REVERSAL_TIMEOUT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Balance Result URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when you request for the balance
    |
    */
    'balance_result_url' => env('MPESA_STATUS_RESULT_URL', ''),

    /*
    |--------------------------------------------------------------------------
    | Balance Timeout URL
    |--------------------------------------------------------------------------
    |
    | This is the URL that safaricom will call when the balance request times out
    |
    */
    'balance_timeout_url' => env('MPESA_STATUS_TIMEOUT_URL', ''),

    /*******************End of Mpesa options************************************/


    /*******************Start of customization options************************************/
    // This is the customization options for the package, it allows you to customize what
    // you want to use in the package, e.g. models, controllers, views etc.
    // It also enables you to specify features that you want to enable or disable since
    // not all features are needed in all applications. 
    // This is useful when you want to customize the package to fit your needs.

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | This is a list of models that are used by the package, it's usefull especially
    | when in need of customizing the models to add more fields or relationships etc.
    | Moreover, it's also useful when in a multi-tenant application where you need
    | to have different models for different tenants.
    |
    */
    'models' => [
        'token' => \Ghostscypher\Mpesa\Models\MpesaToken::class, // The model for the mpesa tokens, most of the time you won't need to change this
        'stk_push' => \Ghostscypher\Mpesa\Models\MpesaStkPush::class, // The model for the STK push transactions
        'log' => \Ghostscypher\Mpesa\Models\MpesaLog::class, // The model for the logs, HTTP requests and responses
    ],

    /*
    |--------------------------------------------------------------------------
    | Features
    |--------------------------------------------------------------------------
    |
    | This is a list of features that affect how the package works, it's useful when
    | you want to enable or disable some features, e.g. you may want to disable the
    | STK push feature if you are not using it in your application.
    |
    */
    'features' => [
        'process_stk_push' => env('MPESA_FEATURE_PROCESS_STK_PUSH', true), // Enable this to process STK push 
                                    // transactions by storing them in the database, 
                                    // this also enables the package to process callbacks
        // 'c2b' => true, // Enable C2B feature
        // 'b2c' => true, // Enable B2C feature
        // 'b2b' => true, // Enable B2B feature
        // 'reversal' => true, // Enable reversal feature
        // 'balance' => true, // Enable balance feature
        // 'status' => true, // Enable status feature,
        'enable_logging' => env('MPESA_FEATURE_ENABLE_LOGGING', false), // Enable logging of the transactions, this will store all the requests and responses in the database, 
                            // useful for debugging, by default this is disabled
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Listeners
    |--------------------------------------------------------------------------
    |
    | This is a list of listeners that will be used to log the requests and responses
    | of the API calls, this is useful when you want to log the requests and responses
    | for debugging purposes.
    |
    | Note: This will be used only if the feature for logging is enabled
    |
    */
    'log_listeners' => [
        \Ghostscypher\Mpesa\Listeners\LogRequestSending::class, // The listener for logging the request sending
        \Ghostscypher\Mpesa\Listeners\LogResponseReceived::class, // The listener for logging the response received
    ],

    /*
    |--------------------------------------------------------------------------
    | Enable Mpesa IP Whitelisting
    |--------------------------------------------------------------------------
    |
    | This is a flag that determines whether to enable IP whitelisting for the
    | mpesa callbacks, this is useful when you want to ensure that the callbacks
    | are secure and only safaricom can make the callbacks to your application.
    |
    | By default, this is enabled to ensure that the callbacks are secure.
    */
    'enable_ip_whitelisting' => env('MPESA_ENABLE_IP_WHITELISTING', true),

    /*
    |--------------------------------------------------------------------------
    | Mpesa whitelisted IPs
    |--------------------------------------------------------------------------
    |
    | This is a list of IPs that are whitelisted by safaricom, these are the IPs
    | that safaricom will use to make the callbacks to your application, it's
    | important to whitelist these IPs to ensure that the callbacks are not blocked
    | by your firewal. It also ensures that the callbacks are secure and only safaricom
    |
    | @see https://developer.safaricom.co.ke/Documentation under Introduction -> Callback and IP Whitelisting
    */
    'whitelisted_ips' => [
        '196.201.214.200',
        '196.201.214.206',
        '196.201.213.114',
        '196.201.214.207',
        '196.201.214.208',
        '196.201.213.44',
        '196.201.212.127',
        '196.201.212.138',
        '196.201.212.129',
        '196.201.212.136',
        '196.201.212.74',
        '196.201.212.69',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allow Mpesa IPs fuzzy matching
    |--------------------------------------------------------------------------
    |
    | This is a flag that determines whether to allow fuzzy matching of the IPs
    | in the whitelist, this is useful when you want to allow IPs that are close
    | to the whitelisted IPs, this is useful when the IPs are not static and you
    | want to allow a range of IPs.
    | The fuzzy search will allow any IPs matching the first 3 octets of the IP
    | i.e. 
    | - 196.201.214.XXX will match all IPs that start with 196.201.214
    | - 196.201.213.XXX will match all IPs that start with 196.201.213
    | - 196.201.212.XXX will match all IPs that start with 196.201.212
    |
    | If in doubt, it's recommended to set this to false to ensure that only the
    | exact IPs are allowed.
    |
    | You can check ownership of the IPs by doing a reverse lookup of the IPs
    | @see https://ip-netblocks.whoisxmlapi.com/
    */
    'allow_fuzzy_matching' => env('MPESA_ALLOW_FUZZY_MATCHING', false),

    /*
    |--------------------------------------------------------------------------
    | Mpesa Fuzzy match IPs
    |--------------------------------------------------------------------------
    |
    | This is a list of IPs that are used for fuzzy matching, these are the IPs
    | that will be used to match the IPs in the whitelist, this is useful when
    | you want to allow a range of IPs that are close to the whitelisted IPs.
    |
    | Note: This is only used when the allow_fuzzy_matching is set to true
    */
    'fuzzy_match_ips' => [
        '196.201.214',
        '196.201.213',
        '196.201.212',
    ],

    /*******************End of customization options************************************/
];
