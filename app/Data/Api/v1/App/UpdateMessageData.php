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
use Carbon\Carbon;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\RequiredUnless;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class UpdateMessageData extends BaseData
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, setTimeZone: 'UTC')]
        #[Date]
        public Carbon $deliveredAt,

        #[In('Processed', 'Sent', 'Delivered', 'Failed')]
        public string $status,

        #[RequiredUnless('status', 'Processed')]
        public ?int $resultCode,

        public ?int $errorCode,
    ) {}

    public static function authorize(#[RouteParameter('device')] Device $device): bool
    {
        return Gate::allows('app', $device);
    }
}