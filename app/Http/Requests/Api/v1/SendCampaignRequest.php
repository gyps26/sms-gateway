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

namespace App\Http\Requests\Api\v1;

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
            'name' => ['sometimes', 'string', 'max:255'],
            'scheduled_at' => ['sometimes', 'date'],
            'recurring' => ['sometimes', 'boolean'],
            'frequency' => ['required_if:recurring,true', 'prohibited_unless:recurring,true', 'integer', 'min:1'],
            'frequency_unit' => ['required_if:recurring,true', 'prohibited_unless:recurring,true', new Enum(FrequencyUnit::class)],
            'ends_at' => ['sometimes', 'prohibited_unless:recurring,true', 'date'],
            'timezone' => ['required_with:active_hours,days_of_week,scheduled_at,ends_at', 'timezone'],
            'days_of_week' => ['sometimes', 'array'],
            'days_of_week.*' => ['distinct', Rule::in([1, 2, 3, 4, 5, 6, 7])],
            'active_hours' => ['sometimes', 'array', 'size:2'],
            'active_hours.start' => ['required_with:active_hours.end', 'date_format:H:i', 'before:active_hours.end'],
            'active_hours.end' => ['required_with:active_hours.start', 'date_format:H:i', 'after:active_hours.start']
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the campaign.',
                'example' => 'My Campaign',
            ],
            'scheduled_at' => [
                'description' => 'The date and time the campaign is scheduled to run.',
                'example' => '2021-01-01T12:00:00Z',
            ],
            'recurring' => [
                'description' => 'Whether the campaign is recurring.',
                'example' => true,
            ],
            'frequency' => [
                'description' => 'The frequency of the campaign.',
                'example' => 1,
            ],
            'frequency_unit' => [
                'description' => 'The unit of the frequency.',
                'example' => 'day',
            ],
            'ends_at' => [
                'description' => 'The date and time when the recurring campaign ends.',
                'example' => '2021-01-01T12:00:00Z',
            ],
            'timezone' => [
                'description' => 'The timezone of the campaign. This is used for scheduled_at, ends_at, days_of_week, and active_hours.',
                'example' => 'UTC',
            ],
            'days_of_week.*' => [
                'description' => 'The days of the week when the campaign is active.',
                'example' => 1,
            ],
            'active_hours.start' => [
                'description' => 'The start time of the active hours.',
                'example' => '08:00',
            ],
            'active_hours.end' => [
                'description' => 'The end time of the active hours.',
                'example' => '17:00',
            ],
        ];
    }
}
