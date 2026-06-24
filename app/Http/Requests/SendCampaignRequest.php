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

namespace App\Http\Requests;

use App\Enums\FrequencyUnit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SendCampaignRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'scheduled_at' => ['nullable', 'date'],
            'recurring' => ['required', 'boolean'],
            'frequency' => ['exclude_unless:recurring,true', 'nullable', 'required_if:recurring,true', 'integer', 'min:1'],
            'frequency_unit' => ['exclude_unless:recurring,true', 'nullable', 'required_if:recurring,true', new Enum(FrequencyUnit::class)],
            'ends_at' => ['exclude_unless:recurring,true', 'nullable', 'date'],
            'timezone' => ['required', 'timezone'],
            'days_of_week' => ['required', 'array'],
            'days_of_week.*' => ['distinct', Rule::in([1, 2, 3, 4, 5, 6, 7])],
            'active_hours' => ['required', 'array', 'size:2'],
            'active_hours.start' => ['required_with:active_hours.end', 'date_format:H:i', 'before:active_hours.end'],
            'active_hours.end' => ['required_with:active_hours.start', 'date_format:H:i', 'after:active_hours.start'],
        ];
    }
}
