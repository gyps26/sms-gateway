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

use App\Enums\MessageType;
use App\Models\MessageGateway;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

trait SendingServerValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function sendingServerRules(): array
    {
        $messageGateways = MessageGateway::all();

        $rules = [
            'name' => ['required', 'string'],
            'driver' => ['required', Rule::in($messageGateways->keys())],
            'supported_types' => ['required', 'array'],
            'supported_types.*' => ['required', 'distinct', new Enum(MessageType::class)],
            'config' => ['required', 'array'],
            'enabled' => ['required', 'boolean'],
        ];

        /** @var MessageGateway $messageGateway */
        $messageGateway = $messageGateways->get(Request::input('driver'));
        if ($messageGateway) {
            foreach ($messageGateway->fields as $field => $config) {
                $rules["config.$field"] = [$config['required'] ? 'required' : 'nullable'];

                switch ($config['type']) {
                    case 'text':
                    case 'list':
                        $rules["config.$field"][] = 'string';
                        break;
                    case 'boolean':
                        $rules["config.$field"][] = 'boolean';
                        break;
                    case 'number':
                        $rules["config.$field"][] = 'numeric';
                        break;
                    case 'dictionary':
                        $rules["config.$field"][] = 'array';
                        $rules["config.$field.*"] = ['required', 'array'];
                        $rules["config.$field.*.key"] = ['required', 'string'];
                        $rules["config.$field.*.value"] = ['required', 'string'];
                        break;
                }

                if (isset($config['options'])) {
                    $rules["config.$field"][] = Rule::in($config['options']);
                }
            }
        }

        return $rules;
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The name of the sending server',
                'example' => 'Twilio',
            ],
            'driver' => [
                'description' => 'The driver of the sending server',
                'example' => 'twilio',
            ],
            'supported_types' => [
                'description' => 'The supported message types by the sending server',
                'example' => ['SMS', 'MMS'],
            ],
            'config' => [
                'description' => 'The configuration for the sending server',
                'example' => [
                    'account_sid' => 'ACXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX',
                    'auth_token' => 'your_auth_token',
                    'from' => '+15017122661',
                ],
            ],
            'enabled' => [
                'description' => 'The status of the sending server',
                'example' => true,
            ],
        ];
    }
}
