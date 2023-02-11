<?php

return [
    "tmn_code" => env('VNPAY_CMN_CODE', ''),
    'hash_secret' => env('VNPAY_HASH_SECRET', ''),
    'url' => env('VNPAY_URL', ''),
    'return_url' => env('VNPAY_RETURN_URL', ''),
    'api_url' => env('VNPAY_API_URL', ''),
    'api_refund' => env('VNPAY_API_REFUND', 'http://sandbox.vnpayment.vn/merchant_webapi/api/transaction')
];
