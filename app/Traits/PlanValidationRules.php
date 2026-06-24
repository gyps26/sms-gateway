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

namespace App\Traits;

use App\Enums\IntervalUnit;
use Illuminate\Validation\Rules\Enum;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

trait PlanValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function planRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'decimal:0,4'],
            'interval' => ['required', 'numeric', 'min:1', 'max:255'],
            'interval_unit' => ['required', 'string', new Enum(IntervalUnit::class)],
            'currency' => ['required', 'string', new Enum(CurrencyAlpha3::class)],
            'features' => ['required', 'array'],
            'features.credits' => ['nullable', 'numeric', 'min:0'],
            'features.contacts' => ['nullable', 'numeric', 'min:0'],
            'features.contact_lists' => ['nullable', 'numeric', 'min:0'],
            'features.devices' => ['nullable', 'numeric', 'min:0'],
            'features.sending_servers' => ['nullable', 'numeric', 'min:0'],
            'features.sender_ids' => ['nullable', 'numeric', 'min:0'],
            'features.templates' => ['nullable', 'numeric', 'min:0'],
            'features.webhooks' => ['nullable', 'numeric', 'min:0'],
            'features.api_tokens' => ['nullable', 'numeric', 'min:0'],
            'features.auto_responses' => ['nullable', 'numeric', 'min:0'],
            'features.data_export' => ['required', 'boolean'],
            'position' => ['required', 'numeric', 'min:0'],
            'enabled' => ['required', 'boolean'],
        ];
    }
}
