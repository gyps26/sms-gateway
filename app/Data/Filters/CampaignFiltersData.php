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
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Spatie\LaravelData\Attributes\Validation\AfterOrEqual;
use Spatie\LaravelData\Attributes\Validation\Bail;
use Spatie\LaravelData\Attributes\Validation\BeforeOrEqual;
use Spatie\LaravelData\Attributes\Validation\DateFormat;
use Spatie\LaravelData\Support\Validation\References\FieldReference;

class CampaignFiltersData extends BaseData
{
    public function __construct(
        public ?CampaignStatus $status = null,
        public ?CampaignType $type = null,
        public ?bool $recurring = null,
        #[Bail, DateFormat('Y-m-d'), BeforeOrEqual(new FieldReference('before'))]
        public ?string $after = null,
        #[Bail, DateFormat('Y-m-d'), AfterOrEqual(new FieldReference('after'))]
        public ?string $before = null
    ) {}

    public function queryParameters(): array
    {
        return [
            'status' => [
                'description' => 'Filter by campaign status',
                'example' => 'Sent',
            ],
            'type' => [
                'description' => 'Filter by campaign type',
                'example' => 'MMS',
            ],
            'recurring' => [
                'description' => 'Filter by recurring campaigns',
                'example' => 'true',
            ],
            'after' => [
                'description' => 'Filter by campaigns after a certain date',
                'example' => '2021-01-01',
            ],
            'before' => [
                'description' => 'Filter by campaigns before a certain date',
                'example' => '2025-01-01',
            ],
        ];
    }
}
