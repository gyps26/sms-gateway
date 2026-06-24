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
use App\Models\Quota;
use Carbon\Carbon;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class UpdateQuotaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(#[RouteParameter('quota')] Quota $quota): bool
    {
        return Gate::allows('update', $quota);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'enabled' => ['required', 'boolean'],
            'value' => ['required_if_accepted:enabled', 'exclude_unless:enabled,true', 'numeric', 'min:1'],
            'frequency' => ['required_if_accepted:enabled', 'exclude_unless:enabled,true', 'numeric', 'min:1'],
            'frequency_unit' => ['required_if_accepted:enabled', 'exclude_unless:enabled,true', 'string', new Enum(FrequencyUnit::class)],
            'reset_at' => ['nullable', 'exclude_unless:enabled,true', 'date'],
            'timezone' => ['nullable', 'exclude_unless:enabled,true', 'timezone'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('enabled') && $this->input('reset_at')) {
                $resetAt = Carbon::parse($this->input('reset_at'), $this->input('timezone'));

                if ($resetAt->lt(now())) {
                    $validator->errors()->add(
                        'reset_at',
                        __('validation.after', ['attribute' => __('validation.attributes.reset_at'), 'date' => 'now'])
                    );
                }
            }
        });
    }
}
