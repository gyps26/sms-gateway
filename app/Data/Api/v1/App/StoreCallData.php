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

namespace App\Data\Api\v1\App;

use App\Data\BaseData;
use App\Models\Device;
use App\Rules\MobileNumber;
use Carbon\Carbon;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Rule;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class StoreCallData extends BaseData
{
    public function __construct(
        #[Rule(new MobileNumber())]
        public string $number,

        #[In(1, 2, 3, 4, 5, 6, 7)]
        public int $type,

        public int $simSlot,

        #[WithCast(DateTimeInterfaceCast::class, setTimeZone: 'UTC')]
        #[Date]
        public Carbon $startedAt,

        #[Min(0)]
        public int $duration
    ) {}

    public static function rules(ValidationContext $context, #[RouteParameter('device')] Device $device): array
    {
        return [
            'sim_slot' => ['required', new In($device->sims->pluck('slot')->toArray())],
        ];
    }

    public static function authorize(#[RouteParameter('device')] Device $device): bool
    {
        return Gate::allows('app', $device);
    }
}