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

use App\Enums\WebhookEvent;
use App\Models\Webhook;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait WebhookValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function webhookRules(?Webhook $webhook = null): array
    {
        return [
            'url' => [
                'required',
                'url',
                Rule::unique('webhooks')
                    ->where('user_id', Auth::id())
                    ->ignore($webhook)
            ],
            'secret' => ['nullable', 'string', 'min:8'],
            'events' => ['required', 'array', 'min:1'],
            'events.*' => ['required', 'distinct', new Enum(WebhookEvent::class)],
        ];
    }
}
