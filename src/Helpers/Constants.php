<?php

namespace Ghostscypher\Mpesa\Helpers;

class Constants
{
    // Identifier types
    public const MPESA_IDENTIFIER_TYPE_MSISDN = '1';

    public const MPESA_IDENTIFIER_TYPE_TILL = '2';

    public const MPESA_IDENTIFIER_TYPE_PAYBILL = '4';

    public const MPESA_IDENTIFIER_TYPE_SHORTCODE = '4';

    public const MPESA_IDENTIFIER_TYPE_REVERSAL = '11';

    // Command ids
    public const MPESA_COMMAND_ID_TRANSACTION_REVERSAL = 'TransactionReversal';

    public const MPESA_COMMAND_ID_SALARY_PAYMENT = 'SalaryPayment';

    public const MPESA_COMMAND_ID_BUSINESS_PAYMENT = 'BusinessPayment';

    public const MPESA_COMMAND_ID_PROMOTION_PAYMENT = 'PromotionPayment';

    public const MPESA_COMMAND_ID_ACCOUNT_BALANCE = 'AccountBalance';

    public const MPESA_COMMAND_ID_CUSTOMER_PAYBILL_ONLINE = 'CustomerPayBillOnline';

    public const MPESA_COMMAND_ID_CUSTOMER_BUY_GOODS_ONLINE = 'CustomerBuyGoodsOnline';

    public const MPESA_COMMAND_ID_TRANSACTION_STATUS_QUERY = 'TransactionStatusQuery';

    public const MPESA_COMMAND_ID_CHECK_IDENTITY = 'CheckIdentity';

    public const MPESA_COMMAND_ID_BUSINESS_PAY_BILL = 'BusinessPayBill';

    public const MPESA_COMMAND_ID_BUSINESS_PAY_BUY_GOODS = 'BusinessBuyGoods';

    public const MPESA_COMMAND_ID_DISBURSE_FUNDS_TO_BUSINESS = 'DisburseFundsToBusiness';

    public const MPESA_COMMAND_ID_BUSINESS_TO_BUSINESS_TRANSFER = 'BusinessToBusinessTransfer';

    public const MPESA_COMMAND_ID_TRANSFER_FROM_MMF_TO_UTILITY = 'BusinessTransferFromMMFToUtility';

    public const MPESA_COMMAND_ID_MERCHANT_TO_MERCHANT_TRANSFER = 'MerchantToMerchantTransfer';

    public const MPESA_COMMAND_ID_MERCHANT_FROM_MERCHANT_TO_WORKING = 'MerchantTransferFromMerchantToWorking';

    public const MPESA_COMMAND_ID_MERCHANT_TO_MMF = 'MerchantServicesMMFAccountTransfer';

    public const MPESA_COMMAND_ID_AGENCY_FLOAT_ADVANCE = 'AgencyFloatAdvance';
}
