<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

namespace App\Data\Filters;

use App\Data\BaseData;
use App\Enums\MessageStatus;
use App\Enums\MessageType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\Bail;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Prohibits;
use Spatie\LaravelData\Support\Validation\References\FieldReference;

class MessageFiltersData extends BaseData
{
    public function __construct(
        #[Min(1)]
        public ?int $user = null,
        #[Min(1)]
        public ?int $campaign = null,
        #[Min(1), Prohibits('sender_id')]
        public ?int $sim = null,
        #[Min(1), Prohibits('sim')]
        public ?int $senderId = null,
        #[Max(16)]
        public ?string $mobileNumber = null,
        #[Max(1600)]
        public ?string $message = null,
        public ?MessageType $type = null,
        /** @var array<int, MessageStatus> */
        public ?array $statuses = null,
        #[Bail, DateFormat('Y-m-d'), BeforeOrEqual(new FieldReference('before'))]
        public ?string $after = null,
        #[Bail, DateFormat('Y-m-d'), AfterOrEqual(new FieldReference('after'))]
        public ?string $before = null
    ) {
        if (Auth::check()) {
            $this->user = Auth::user()->is_admin ? $this->user : Auth::id();
        }
    }

    public static function rules(): array
    {
        return [
            'statuses.*' => [new Enum(MessageStatus::class)]
        ];
    }

    public function queryParameters(): array
    {
        return [
            'user' => [
                'description' => 'Filter by user id. Only available for admin.',
                'example' => 1,
            ],
            'campaign' => [
                'description' => 'Filter by campaign id.',
                'example' => 1,
            ],
            'sim' => [
                'description' => 'Filter by sim id. This field is prohibited if sender_id is present.',
                'example' => 1,
            ],
            'sender_id' => [
                'description' => 'Filter by sender id. This field is prohibited if sim is present.',
                'example' => 1,
            ],
            'mobile_number' => [
                'description' => 'Filter by mobile number.',
                'example' => '+12541241241',
            ],
            'message' => [
                'description' => 'Filter by message.',
                'example' => 'Hello, World!',
            ],
            'type' => [
                'description' => 'Filter by message type.',
                'example' => 'SMS',
            ],
            'statuses' => [
                'description' => 'Filter by message statuses.',
                'example' => ['Processed', 'Failed'],
            ],
            'after' => [
                'description' => 'Filter by messages that are sent after this date.',
                'example' => '2021-01-01',
            ],
            'before' => [
                'description' => 'Filter by messages that are sent before this date.',
                'example' => '2024-01-01',
            ],
        ];
    }
}
