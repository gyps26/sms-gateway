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

namespace App\Data;

use App\Enums\CampaignType;
use App\Enums\FrequencyUnit;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Transformers\DateTimeInterfaceTransformer;

class SendCampaignData extends BaseData
{
    #[Computed]
    #[WithTransformer(DateTimeInterfaceTransformer::class, setTimeZone: 'UTC')]
    public ?Carbon $repeatAt;

    public function __construct(
        public ?string $name,

        #[WithTransformer(DateTimeInterfaceTransformer::class, setTimeZone: 'UTC')]
        public ?Carbon $scheduledAt,

        public string $timezone,

        public bool $recurring,

        public ?int $frequency,

        public ?FrequencyUnit $frequencyUnit,

        #[WithTransformer(DateTimeInterfaceTransformer::class, setTimeZone: 'UTC')]
        public ?Carbon $endsAt,

        /** @var array<int, int> */
        public array $daysOfWeek,

        public string $activeHours,

        public CampaignType $type,

        public ?PayloadData $payload,

        public ?OptionsData $options,

        /** @var array<int, int> */
        public ?array $sims,

        /** @var array<int, int> */
        public ?array $senderIds,

        public ?UploadedFile $spreadsheet,

        /** @var array<int, int> */
        public ?array $contactLists,

        /** @var array<int, string|\Spatie\MediaLibrary\MediaCollections\Models\Media|\Illuminate\Http\UploadedFile|> */
        public ?array $attachments,
    ) {
        $this->repeatAt = null;
        if ($this->recurring) {
            $this->repeatAt = transform(
                $this->scheduledAt,
                fn(Carbon $date) => $date->copy()->add($this->frequencyUnit->value, $this->frequency),
                now($this->timezone)->add($this->frequencyUnit->value, $this->frequency)
            );
        }

        sort($this->daysOfWeek);
    }

    public static function fromArray(array $data): SendCampaignData
    {
        $timezone = data_get($data, 'timezone', 'UTC');

        return new self(
            name: data_get($data, 'name'),
            scheduledAt: transform(
                data_get($data, 'scheduled_at'),
                fn($date) => Carbon::parse($date, $timezone)
            ),
            timezone: $timezone,
            recurring: data_get($data, 'recurring', false),
            frequency: data_get($data, 'frequency'),
            frequencyUnit: transform(
                data_get($data, 'frequency_unit'),
                fn($v) => FrequencyUnit::from($v)
            ),
            endsAt: transform(
                data_get($data, 'ends_at'),
                fn($date) => Carbon::parse($date, $timezone)
            ),
            daysOfWeek: data_get($data, 'days_of_week', [1, 2, 3, 4, 5, 6, 7]),
            activeHours: transform(
                data_get($data, 'active_hours'),
                fn($activeHours) => implode('-', $activeHours),
                '00:00-23:59'
            ),
            type: CampaignType::from(
                data_get(
                    target: $data,
                    key: 'type',
                    default: data_get($data, 'ussd_codes') ? 'USSD Pull' : 'SMS'
                )
            ),
            payload: PayloadData::from(
                array_filter(
                    $data,
                    function ($value, $key) {
                        return in_array($key, ['mobile_numbers', 'ussd_codes', 'message']) && isset($value);
                    },
                    ARRAY_FILTER_USE_BOTH
                )
            ),
            options: OptionsData::from(
                array_filter(
                    $data,
                    function ($value, $key) {
                        return in_array($key, ['delay', 'prioritize', 'delivery_report']) && isset($value);
                    },
                    ARRAY_FILTER_USE_BOTH
                )
            ),
            sims: data_get($data, 'sims'),
            senderIds: data_get($data, 'sender_ids'),
            spreadsheet: data_get($data, 'spreadsheet'),
            contactLists: data_get($data, 'contact_lists'),
            attachments: data_get($data, 'attachments')
        );
    }
}
