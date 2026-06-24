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
use App\Enums\UssdPullStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\Bail;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Support\Validation\References\FieldReference;

class UssdPullFiltersData extends BaseData
{
    public function __construct(
        #[Min(1)]
        public ?int $user = null,
        #[Min(1)]
        public ?int $campaign = null,
        #[Min(1)]
        public ?int $sim = null,
        /** @var array<int, UssdPullStatus> */
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
            'statuses.*' => [new Enum(UssdPullStatus::class)]
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
                'description' => 'Filter by sim id.',
                'example' => 1,
            ],
            'statuses' => [
                'description' => 'Filter by USSD pull statuses.',
                'example' => ['Completed', 'Failed'],
            ],
            'after' => [
                'description' => 'Show USSD pulls that are sent after this date.',
                'example' => '2021-01-01',
            ],
            'before' => [
                'description' => 'Show USSD pulls that are sent before this date.',
                'example' => '2021-01-01',
            ],
        ];
    }
}
