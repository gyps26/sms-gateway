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
use App\Rules\MobileNumber;
use Spatie\LaravelData\Attributes\Validation\In;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Rule;

class SimData extends BaseData
{
    public function __construct(
        public int $slot,

        public bool $active,

        #[Max(255)]
        public ?string $name = null,

        #[Rule(new MobileNumber())]
        public ?string $number = null,
        
        #[Max(255)]
        public ?string $carrier = null,
        
        #[Max(2)]
        public ?string $country = null,

        public ?string $icc_id = null,

        public ?string $mcc = null,

        public ?string $mnc = null,

        public ?bool $data_roaming = null,
        
        #[Min(0), Max(4)]
        public ?int $signal_strength = null,
    ) {}
}