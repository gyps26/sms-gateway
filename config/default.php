<?php

use App\Enums\DayOfWeek;
use App\Enums\MessageType;

return [
    'send_messages' => [
        'delay' => '2-5',
        'recipients' => 'mobile_numbers',
        'type' => MessageType::Mms,
        'days_of_week' => DayOfWeek::values(),
        'active_hours' => '00:00-23:59',
        'delivery_report' => false,
        'timezone' => 'UTC',
    ],
    'send_ussd_pulls' => [
        'delay' => '30',
        'days_of_week' => DayOfWeek::values(),
        'active_hours' => '00:00-23:59',
        'timezone' => 'UTC',
    ]
];
