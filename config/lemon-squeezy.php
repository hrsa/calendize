<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lemon Squeezy API Key
    |--------------------------------------------------------------------------
    |
    | The Lemon Squeezy API key is used to authenticate with the Lemon Squeezy
    | API. You can find your API key in the Lemon Squeezy dashboard. You can
    | find your API key in the Lemon Squeezy dashboard under the "API" section.
    |
    */

    'api_key' => env('LEMON_SQUEEZY_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Lemon Squeezy Signing Secret
    |--------------------------------------------------------------------------
    |
    | The Lemon Squeezy signing secret is used to verify that the webhook
    | requests are coming from Lemon Squeezy. You can find your signing
    | secret in the Lemon Squeezy dashboard under the "Webhooks" section.
    |
    */

    'signing_secret' => env('LEMON_SQUEEZY_SIGNING_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Lemon Squeezy Url Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI where routes from Lemon Squeezy will be served
    | from. The URL built into Lemon Squeezy is used by default; however,
    | you can modify this path as you see fit for your application.
    |
    */

    'path' => env('LEMON_SQUEEZY_PATH', 'lemon-squeezy'),

    /*
    |--------------------------------------------------------------------------
    | Lemon Squeezy Store
    |--------------------------------------------------------------------------
    |
    | This is the ID of your Lemon Squeezy store. You can find your store
    | ID in the Lemon Squeezy dashboard. The entered value should be the
    | part after the # sign.
    |
    */

    'store' => env('LEMON_SQUEEZY_STORE'),
    'sales' => [
        'beginner' => [
            'product'  => (string) env('LEMON_SQUEEZY_PRODUCT_BEGINNER'),
            'variant'  => (string) env('LEMON_SQUEEZY_VARIANT_BEGINNER'),
            'credits'  => (int) env('LEMON_SQUEEZY_CREDITS_BEGINNER', 10),
            'rollover' => (int) env('LEMON_SQUEEZY_ROLLOVER_BEGINNER', 2),
        ],
        'classic' => [
            'product'  => (string) env('LEMON_SQUEEZY_PRODUCT_CLASSIC'),
            'variant'  => (string) env('LEMON_SQUEEZY_VARIANT_CLASSIC'),
            'credits'  => (int) env('LEMON_SQUEEZY_CREDITS_CLASSIC', 25),
            'rollover' => (int) env('LEMON_SQUEEZY_ROLLOVER_CLASSIC', 5),
        ],
        'power' => [
            'product'  => (string) env('LEMON_SQUEEZY_PRODUCT_POWER'),
            'variant'  => (string) env('LEMON_SQUEEZY_VARIANT_POWER'),
            'credits'  => (int) env('LEMON_SQUEEZY_CREDITS_POWER', 100),
            'rollover' => (int) env('LEMON_SQUEEZY_ROLLOVER_POWER', 20),
        ],
        'topup' => [
            'product' => (string) env('LEMON_SQUEEZY_PRODUCT_TOPUP'),
            'variant' => (string) env('LEMON_SQUEEZY_VARIANT_TOPUP'),
            'credits' => (int) env('LEMON_SQUEEZY_CREDITS_TOPUP', 5),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Redirect URL
    |--------------------------------------------------------------------------
    |
    | This is the default redirect URL that will be used when a customer
    | is redirected back to your application after completing a purchase
    | from a checkout session in your Lemon Squeezy store.
    |
    */

    'redirect_url' => null,

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('LEMON_SQUEEZY_CURRENCY_LOCALE', 'en'),

];
