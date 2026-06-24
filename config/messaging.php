<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Message To Email
    |--------------------------------------------------------------------------
    |
    | When enabled, the system will forward received messages to user's email.
    | This requires mail to be configured properly.
    |
    */

    'message_to_email' => false,

    /*
    |--------------------------------------------------------------------------
    | Email To Message
    |--------------------------------------------------------------------------
    |
    | When enabled, the system will forward incoming emails in specific format to messages.
    | This requires imap to be configured properly.
    |
    */

    'email_to_message' => false,

    'sim' => [

        /*
        |--------------------------------------------------------------------------
        | Wait For Confirmation
        |--------------------------------------------------------------------------
        |
        | When enabled, the system will wait for the network operator to confirm message status
        | before sending another message.
        |
        */

        'wait_for_confirmation' => true,
    ],

    'auto_retry' => [

        /*
        |----------------------------------------------------------------------
        | Auto Retry
        |----------------------------------------------------------------------
        |
        | When enabled, the system will automatically retry failed messages.
        |
        */

        'enabled' => false,

        /*
        |----------------------------------------------------------------------
        | Max Attempts
        |----------------------------------------------------------------------
        |
        | The maximum number of retry attempts before marking the message as failed
        | permanently.
        |
        */

        'max_attempts' => 3,

        /*
        |----------------------------------------------------------------------
        | Change After
        |----------------------------------------------------------------------
        |
        | How many failed attempts before switch to the most successful SIM or Sender ID.
        | You can disable this by setting it to null.
        |
        */

        'change_after' => 0,
    ],

    'prompts' => [

        /*
        |----------------------------------------------------------------------
        | Keywords
        |----------------------------------------------------------------------
        |
        | Define keywords that trigger specific actions.
        |
        */

        'keywords' => [
            'blacklist' => 'STOP',
            'whitelist' => 'START',
            'subscribe' => 'SUBSCRIBE',
            'unsubscribe' => 'UNSUBSCRIBE',
        ],

        /*
        |----------------------------------------------------------------------
        | Notify
        |----------------------------------------------------------------------
        |
        | Whether to notify the sender after processing a prompt keyword.
        |
        */

        'notify' => true,
    ],

    'phone_id' => [

        /*
        |----------------------------------------------------------------------
        | Identify Phone Numbers
        |----------------------------------------------------------------------
        |
        | When enabled, the system will attempt to show the information from the contact
        | field instead of just phone number if the phone number exists in the user's
        | contact lists.
        |
        */

        'enabled' => true,

        /*
        |----------------------------------------------------------------------
        | Contact Field Tag
        |----------------------------------------------------------------------
        |
        | The contact field tag to use for identifying phone numbers.
        |
        */

        'contact_field_tag' => 'name'
    ],
];