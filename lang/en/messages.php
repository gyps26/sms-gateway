<?php

return [
    'auto_response' => [
        'created' => 'Auto response created successfully.',
        'updated' => 'Auto response updated successfully.',
        'deleted' => 'Auto response deleted successfully.'
    ],
    'blacklist' => [
        'created' => 'Successfully added a new entry to blacklist.|Successfully added new entries to blacklist.',
        'delete' => [
            'failed' => 'No mobile numbers were deleted from blacklist.',
            'success' => 'A mobile number deleted successfully.|:count mobile numbers deleted successfully.',
        ]
    ],
    'coupon' => [
        'created' => 'Coupon created successfully.',
        'updated' => 'Coupon updated successfully.'
    ],
    'contact' => [
        'created' => 'Contact added successfully.',
        'updated' => 'Contact updated successfully.',
        'import' => [
            'queued' => 'Contacts are being imported. You will be notified once the process is completed.',
            'cancelled' => 'Import process has been cancelled. It may take a few minutes to stop the process.',
            'not_running' => 'Import job is not running or it has already finished.',
        ],
        'export' => [
            'queued' => 'Contacts are being exported. You will be notified once the process is completed.',
        ],
        'delete' => [
            'failed' => 'No contacts were deleted from contact list.',
            'success' => 'A contact deleted successfully.|:count contacts deleted successfully.',
        ],
    ],
    'contact_list' => [
        'created' => 'Contact list created successfully.',
        'updated' => 'Contact list updated successfully.',
        'deleted' => 'Contact list deleted successfully.'
    ],
    'call' => [
        'delete' => [
            'failed' => 'No calls were deleted from call log.',
            'success' => 'A call deleted successfully.|:count calls deleted successfully.'
        ]
    ],
    'campaign' => [
        'created' => 'Campaign created successfully.',
        'updated' => 'Campaign updated successfully.',
        'retried' => 'Campaign retried successfully.',
        'unable_to_retry' => 'It is not possible to retry this campaign.',
        'unable_to_cancel' => 'It is not possible to cancel this campaign.',
        'delete' => [
            'failed' => 'No campaigns were deleted.',
            'success' => 'A campaign deleted successfully.|:count campaigns deleted successfully.',
        ],
        'cancelling' => 'Campaign marked for cancellation successfully.',
    ],
    'device' => [
        'updated' => 'Device updated successfully.',
        'deleted' => 'Device deleted successfully.',
        'shared' => 'Device shared successfully.',
        'campaign' => [
            'cancelled' => 'Campaign cancelled successfully for this device.',
            'cancelling' => 'Campaign marked for cancellation successfully for this device.',
            'retried' => 'Campaign retried successfully for this device.',
            'unable_to_retry' => 'It is not possible to retry this campaign for this device.',
        ],
        'register' => [
            'invalid_credentials' => 'The email address or password is incorrect.',
            'invalid_qr_code' => 'The QR code is either expired or not valid.',
            '2fa' => [
                'required' => 'The two factor authentication code is required.',
                'incorrect' => 'The two factor authentication code is incorrect.'
            ]
        ]
    ],
    'field' => [
        'created' => 'Field added successfully.',
        'updated' => 'Field updated successfully.',
        'deleted' => 'The ":label" field deleted successfully.'
    ],
    'message' => [
        'delete' => [
            'failed' => 'No messages were deleted.',
            'success' => 'A message deleted successfully.|:count messages deleted successfully.',
        ],
        'retry' => [
            'queued' => 'Messages are being retried. You will be notified once the process is completed.',
            'failed' => 'No messages were retried.',
            'success' => 'A message retried successfully.|:count messages retried successfully.',
        ],
    ],
    'payment' => [
        'approved' => 'Payment approved successfully.',
        'declined' => 'Payment declined successfully.',
        'completed' => 'Payment successful. Your subscription will be activated shortly.',
    ],
    'plan' => [
        'created' => 'Plan created successfully.',
        'updated' => 'Plan updated successfully.',
        'disabled' => 'It is not possible to subscribe to this plan because it is disabled.',
        'downgradeNotAllowed' => 'It is not possible to downgrade to this plan due to over the limit criteria.',
        'alreadySubscribed' => 'There is already an active subscription on this user account.',
    ],
    'quota' => [
        'updated' => 'Quota updated successfully.',
    ],
    'sender_id' => [
        'created' => 'Sender ID created successfully.',
        'deleted' => 'Sender ID deleted successfully.',
        'shared' => 'Sender ID shared successfully.',
    ],
    'sending_server' => [
        'created' => 'Sending server created successfully.',
        'updated' => 'Sending server updated successfully.',
        'deleted' => 'Sending server deleted successfully.',
        'campaign' => [
            'cancelled' => 'Campaign cancelled successfully for this sending server.',
            'cancelling' => 'Campaign marked for cancellation successfully for this sending server.',
            'retried' => 'Campaign retried successfully for this sending server.',
            'unable_to_retry' => 'It is not possible to retry this campaign for this sending server.',
        ],
    ],
    'sim' => [
        'updated' => 'Sim updated successfully.',
    ],
    'subscription' => [
        'assigned' => 'Subscription assigned successfully.',
        'started' => 'Subscription started successfully.',
        'cancelled' => 'Subscription cancelled successfully.',
        'updated' => 'Subscription updated successfully.',
    ],
    'tax' => [
        'created' => 'Tax created successfully.',
        'updated' => 'Tax updated successfully.',
        'deleted' => 'Tax deleted successfully.',
    ],
    'template' => [
        'created' => 'Template created successfully.',
        'updated' => 'Template updated successfully.',
        'deleted' => 'Template deleted successfully.',
    ],
    'user' => [
        'created' => 'User created successfully.',
        'deleted' => 'User deleted successfully.',
    ],
    'ussd_pull' => [
        'delete' => [
            'failed' => 'No USSD pull requests were deleted.',
            'success' => 'A USSD pull request deleted successfully.|:count USSD pull requests deleted successfully.',
        ],
        'retry' => [
            'queued' => 'USSD pull requests are being retried. You will be notified once the process is completed.',
            'failed' => 'No USSD pull requests were retried.',
            'success' => 'A USSD pull request retried successfully.|:count USSD pull requests retried successfully.',
        ],
    ],
    'webhook_call' => [
        'resent' => 'Webhook call resent successfully.',
    ],
    'webhook' => [
        'created' => 'Webhook created successfully.',
        'updated' => 'Webhook updated successfully.',
        'deleted' => 'Webhook deleted successfully.'
    ],
    'prompts' => [
        'blacklist' => 'You will not receive any more messages from us.',
        'whitelist' => 'Your number has been successfully removed from the blacklist.',
        'whitelist_or_subscribe' => 'Just reply ":prompt" if you change your mind.',
        'subscribe' => 'Your number has been successfully subscribed to the contact list.',
        'unsubscribe' => 'Your number has been successfully unsubscribed from the contact list.',
        'general' => 'Let us know if you change your mind.'
    ],
    'payment_gateway' => [
        'currency_not_supported' => 'The currency is not supported.',
        'crypto_com' => [
            'interval_not_supported' => 'Only monthly plans are supported by Crypto.com.'
        ],
        'paypal' => [
            'interval_not_supported' => 'Interval value is not supported for this interval unit.'
        ]
    ],
    'mailer' => [
        'test' => [
            'success' => 'Test email sent successfully.',
        ]
    ],
    'imap' => [
        'test' => [
            'success' => 'IMAP connection successful.',
        ]
    ],
    'installer' => [
        'errors' => [
            'depedencies' => 'Please ensure all dependencies are installed or enabled before proceeding.',
            'permissions' => 'Please ensure all required permissions are granted before proceeding.',
            'database' => 'The database connection failed with the following error: :message',
            'admin' => 'Please create an admin account to proceed with the installation.',
            'completed' => 'Unable to mark the installation as completed. Please try again.',
        ],
    ],
    'global' => [
        'error' => 'Something went wrong! Please try again later.',
        'limit_exceeded' => 'You have exceeded your plan limit.',
        'not_allowed' => 'Your subscription plan does not allow this action.',
    ]
];
