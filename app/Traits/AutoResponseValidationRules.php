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

use App\Enums\Criterion;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

trait AutoResponseValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function autoResponseRules(): array
    {
        return [
            'messages' => ['required', 'array'],
            'messages.*' => ['required', 'string'],
            'response' => ['required', 'string'],
            'type' => ['required', Rule::in(['SMS', 'MMS'])],
            'attachments' => ['exclude_if:type,SMS', 'nullable', 'array'],
            'attachments.*' => [
                'required',
                File::types([
                    'jpg',
                    'jpeg',
                    'png',
                    'gif',
                    'aac',
                    '3gp',
                    'amr',
                    'mp3',
                    'm4a',
                    'wav',
                    'mp4',
                    'txt',
                    'vcf'
                ])
            ],
            'criterion' => ['required', new Enum(Criterion::class)],
            'enabled' => ['required', 'boolean']
        ];
    }
}
