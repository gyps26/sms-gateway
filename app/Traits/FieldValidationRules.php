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

use App\Enums\FieldType;
use App\Models\Field;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait FieldValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function fieldRules(int $contactListId, ?Field $field = null): array
    {
        $optionsValues = transform(
            Request::input('options'),
            fn($options) => is_array($options) ? Arr::pluck($options, 'value') : []
        );

        $rules = [
            'label' => ['required', 'string', 'min:3', 'max:50'],
            'tag' => [
                'required',
                'alpha_dash',
                'lowercase',
                'min:3',
                'max:50',
                Rule::notIn(['mobile_number', 'subscribed']),
                Rule::unique('fields')
                    ->where('contact_list_id', $contactListId)
                    ->ignore($field)
            ],
            'type' => ['required', new Enum(FieldType::class)],
            'options' => [
                'exclude_if:type,text,textarea,number,email,date,time,datetime-local',
                'required_if:type,checkbox,dropdown,multiselect,radio',
                'array'
            ],
            'options.*.label' => ['required', 'string', 'min:3'],
            'options.*.value' => ['required', 'alpha_dash', 'distinct'],
            'default_value' => [
                'nullable',
                match (Request::input('type')) {
                    'number' => 'numeric',
                    'email' => 'email',
                    'date' => 'date_format:Y-m-d',
                    'datetime-local' => 'date',
                    'time' => 'date_format:H:i',
                    'dropdown', 'radio' => Rule::in($optionsValues),
                    'multiselect', 'checkbox' => 'array',
                    default => 'string',
                }
            ],
            'required' => ['required', 'boolean'],
        ];

        if (in_array(Request::input('type'), ['multiselect', 'checkbox'])) {
            $rules['default_value.*'] = ['required', 'distinct', Rule::in($optionsValues)];
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'label' => [
                'description' => 'The label of the field.',
                'example' => 'First Name',
            ],
            'tag' => [
                'description' => 'The tag of the field.',
                'example' => 'first_name',
            ],
            'type' => [
                'description' => 'The type of the field.',
                'example' => 'text',
            ],
            'options' => [
                'description' => 'List of all possible options for the field.',
            ],
            'options.*.label' => [
                'description' => 'The label of the option.',
                'example' => 'Option 1',
            ],
            'options.*.value' => [
                'description' => 'The value of the option.',
                'example' => 'option_1',
            ],
            'default_value' => [
                'description' => 'The default value of the field.',
                'example' => 'John',
            ],
            'required' => [
                'description' => 'Whether the field is required or not.',
                'example' => true,
            ],
        ];
    }
}
