<?php

return [
    'BankTransfer' => [
        'enabled' => env('BANK_TRANSFER_ENABLED', false),
        'instructions' => env('BANK_TRANSFER_INSTRUCTIONS'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#bank-transfer',
    ],
    'CryptoCom' => [
        'enabled' => env('CRYPTO_COM_ENABLED', false),
        'secret_key' => env('CRYPTO_COM_SECRET_KEY'),
        'webhook_secret' => env('CRYPTO_COM_WEBHOOK_SECRET'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#crypto-com'
    ],
    'PayPal' => [
        'enabled' => env('PAYPAL_ENABLED', false),
        'sandbox' => env('PAYPAL_SANDBOX', true),
        'client_id' => env('PAYPAL_CLIENT_ID'),
        'client_secret' => env('PAYPAL_CLIENT_SECRET'),
        'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#paypal',
    ],
    'Paystack' => [
        'enabled' => env('PAYSTACK_ENABLED', false),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
        'webhook_secret' => env('PAYSTACK_WEBHOOK_SECRET'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#paystack',
    ],
    'Razorpay' => [
        'enabled' => env('RAZORPAY_ENABLED', false),
        'key' => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
        'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#razorpay',
    ],
    'Stripe' => [
        'enabled' => env('STRIPE_ENABLED', false),
        'secret_key' => env('STRIPE_SECRET_KEY'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'docs' => 'https://rbsoft.org/docs/sms-gateway/enhanced/payment-gateway-settings.html#stripe',
    ],
];
