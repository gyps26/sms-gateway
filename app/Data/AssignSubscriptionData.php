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

use App\Data\Casts\ToEloquentModel;
use App\Models\Plan;
use App\Models\User;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\WithCast;

class AssignSubscriptionData extends BaseData
{
    #[Computed]
    public ?Carbon $endsAt;

    #[Computed]
    public ?Carbon $renewalAt;

    public function __construct(
        #[Exists('plans', 'id')]
        #[WithCast(ToEloquentModel::class)]
        public Plan $plan,
        #[Exists('users', 'id')]
        #[WithCast(ToEloquentModel::class)]
        public User $user,
        #[Min(1)]
        public ?int $cycles = null,
    ) {
        $this->renewalAt = is_null($cycles) || $cycles > 1
            ? Carbon::now('UTC')->add($plan->interval_unit->value, $plan->interval)
            : null;

        $this->endsAt = $cycles
            ? Carbon::now('UTC')->add($plan->interval_unit->value, $plan->interval * $cycles)
            : null;
    }
}
