<?php

return [
    'messages' => [
        'received' => [
            'subject' => 'Message received from :from on :to',
        ],
    ],
    'subscriptions' => [
        'started' => [
            'subject' => 'Subscription Started',
            'message' => 'Thank you for subscribing to **:plan** plan! Your subscription has started successfully on :date.',
            'features' => 'You now have access to all the features included in your plan. We’re excited to have you on board!',
            'help' => 'Need help getting started? Visit our [**Documentation**](:docs).',
        ],
        'renewed' => [
            'subject' => 'Subscription Renewed',
            'message' => 'Your subscription has been renewed successfully.',
            'features' => 'You credits have been updated, and you can continue to enjoy all the features included in your plan. You can view your updated subscription details below.',
        ],
        'cancelled' => [
            'subject' => 'Subscription Cancelled',
            'message' => 'Your subscription has been cancelled successfully. We\'re sorry to see you go. If you change your mind, you can always resubscribe later.',
            'features' => 'You will still have access to your plan features until the end of your current billing period.',
        ],
        'expired' => [
            'subject' => 'Subscription Expired',
            'message' => 'Your subscription has been expired. We hope you enjoyed using our service. If you wish to continue using our service, please consider resubscribing.',
        ],
        'next_renewal' => 'Your next renewal date is :date.',
        'ends' => 'Your subscription will end on :date.',
        'contact' => 'If you have any questions, feel free to reach out to us by clicking the "Need Help?" button below.',
        'view' => 'View Your Subscription',
        'need_help' => 'Need Help?',
    ],
    'hi' => 'Hi :name,',
    'best_regards' => 'Best regards,',
];
