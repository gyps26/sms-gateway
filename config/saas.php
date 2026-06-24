<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Billing Information
    |--------------------------------------------------------------------------
    |
    | This information will be used in billing statements and invoices.
    | It typically includes the company name, address, and contact details.
    |
    */

    'billing_info' => env('BILLING_INFO'),

    /*
    |--------------------------------------------------------------------------
    | Credits Configuration
    |--------------------------------------------------------------------------
    |
    | Define the credit system for various services offered by the application.
    | Each service has an associated credit cost that will be deducted from
    | the user's account upon usage.
    |
    */

    'credits' => [
        'received' => [
            'amount' => 1,
        ],

        'sms' => [
            'per_part' => false,
            'amount' => 1,
        ],

        'mms' => [
            'amount' => 1,
        ],

        'ussd_pull' => [
            'amount' => 1,
        ],

        'call' => [
            'amount' => 1,
        ],

        'webhook_call' => [
            'amount' => 1,
        ],

        'message_to_email' => [
            'amount' => 1,
        ],

        'email_to_message' => [
            'amount' => 1,
        ],
    ],

    'trial' => [

        /*
        |----------------------------------------------------------------------
        | Plan ID
        |----------------------------------------------------------------------
        |
        | The identifier of the plan that will be assigned to new users upon registration.
        | Set to null if no plan should be assigned by default.
        |
        */

        'plan_id' => null,

        /*
        |----------------------------------------------------------------------
        | Trial Duration (Days)
        |----------------------------------------------------------------------
        |
        | The number of days a trial period will last for new users.
        | Set to null to default to plan's first cycle duration.
        |
        */

        'duration' => null,

        /*
        |----------------------------------------------------------------------
        | Footer
        |----------------------------------------------------------------------
        |
        | Custom footer text to be displayed during the trial period in sent messages.
        | Set to null to avoid displaying any footer.
        |
        */

        'footer' => null,
    ],
];