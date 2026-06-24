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

use App\Data\Transformers\ToDelimitedString;
use App\Models\Campaign;
use App\Rules\HasSubscribers;
use App\Rules\ExistAndAuthorized;
use App\Rules\MobileNumber;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class CreateMessagesData extends Data
{
    public function __construct(
        public ?string $type,
        public ?string $recipients,
        #[WithTransformer(ToDelimitedString::class)]
        /** @var array<int, string> */
        public ?array $mobile_numbers = [],
        /** @var array<int, int> */
        public ?array $contact_lists = [],
        /** @var array<int, int> */
        public ?array $sims = [],
    ) {}

    public static function rules(ValidationContext $context): array
    {
        return [
            'sims' => [
                'nullable',
                'array',
                new ExistAndAuthorized('activeSims', 'sims.id'),
            ],
            'sims.*' => ['distinct'],
            'sender_ids' => [
                'nullable',
                'array',
                new ExistAndAuthorized('activeSenderIds', 'sender_ids.id'),
            ],
            'sender_ids.*' => ['distinct'],
            'type' => ['nullable', Rule::in(['SMS', 'MMS', 'WhatsApp'])],
            'recipients' => ['nullable', Rule::in(['mobile_numbers', 'contact_lists'])],
            'mobile_numbers' => [
                'required_if:recipients,mobile_numbers',
                'exclude_unless:recipients,mobile_numbers',
                'array'
            ],
            'mobile_numbers.*' => ['distinct', new MobileNumber()],
            'contact_lists' => [
                'required_if:recipients,contact_lists',
                'exclude_unless:recipients,contact_lists',
                'array'
            ],
            'contact_lists.*' => [
                'bail',
                'distinct',
                'required',
                'integer',
                'min:1',
                new HasSubscribers()
            ],
        ];
    }

    public static function authorize(): bool
    {
        return Gate::allows('create', Campaign::class);
    }
}
