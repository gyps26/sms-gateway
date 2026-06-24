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

use App\Models\SenderId;
use App\Models\SendingServer;
use Illuminate\Auth\Access\Response;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSenderIdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(#[RouteParameter('sending_server')] SendingServer $sendingServer): bool|Response
    {
        return when(
            Gate::allows('update', $sendingServer),
            Gate::inspect('create', SenderId::class),
            false
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(#[RouteParameter('sending_server')] SendingServer $sendingServer): array
    {
        return [
            'value' => [
                'required',
                'string',
                'max:11',
                Rule::unique('sender_ids', 'value')->where('sending_server_id', $sendingServer->id)
            ],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'value' => [
                'description' => 'The sender id value.',
                'example' => 'MySenderID',
            ],
        ];
    }
}
