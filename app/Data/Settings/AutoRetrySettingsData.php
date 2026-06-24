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

namespace App\Data\Settings;

use App\Data\BaseData;
use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class AutoRetrySettingsData extends BaseData
{
    public function __construct(
        public bool $enabled,
        #[Min(1)]
        public int $maxAttempts,
        public ?int $changeAfter
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'change_after' => [
                'nullable',
                'integer',
                'min:0',
                Rule::when(
                    filter_var(data_get($context->fullPayload, 'max_attempts'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]),
                    ['max:' . (data_get($context->fullPayload, 'max_attempts') - 1)]
                )
            ]
        ];
    }

    public static function authorize(): bool
    {
        return Gate::allows('updateAny', Setting::class);
    }
}
