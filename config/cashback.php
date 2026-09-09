<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User Cashback Rate (Share of Shopee Affiliate Commission)
    |--------------------------------------------------------------------------
    |
    | The default fraction of Shopee commission passed to the end user.
    | 0.80 means 80% of Shopee commission is shared with the user.
    |
    */
    'rate' => (float) env('CASHBACK_RATE', 0.80),

    /*
    |--------------------------------------------------------------------------
    | Minimum Withdrawal Threshold (VND)
    |--------------------------------------------------------------------------
    |
    | Minimum amount required to request a withdrawal to bank account.
    |
    */
    'min_withdrawal' => (float) env('CASHBACK_MIN_WITHDRAWAL', 50000),

    /*
    |--------------------------------------------------------------------------
    | Shopee Open Platform Affiliate API Configuration
    |--------------------------------------------------------------------------
    */
    'shopee' => [
        'app_id' => env('SHOPEE_AFFILIATE_APP_ID', ''),
        'secret' => env('SHOPEE_AFFILIATE_SECRET', ''),
        'endpoint' => env('SHOPEE_AFFILIATE_ENDPOINT', 'https://open-api.affiliate.shopee.vn/graphql'),
        'mock_enabled' => (bool) env('SHOPEE_MOCK_ENABLED', true),
    ],
];
