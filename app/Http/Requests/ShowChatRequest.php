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

use App\Models\User;
use App\Rules\ExistAndAuthorized;
use App\Rules\MobileNumber;
use App\Rules\RequireWithButWithout;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Foundation\Http\FormRequest;

class ShowChatRequest extends FormRequest
{
    /**
     * The route that users should be redirected to if validation fails.
     *
     * @var string
     */
    protected $redirectRoute = 'chat';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(#[CurrentUser] User $user): array
    {
        return [
            'mobile_number' => ['nullable', 'required_with:sim,sender_id', new MobileNumber()],
            'sim' => [
                'bail',
                'nullable',
                new RequireWithButWithout('mobile_number', 'sender_id'),
                'prohibits:sender_id',
                'integer',
                new ExistAndAuthorized('activeSims', 'sims.id')
            ],
            'sender_id' => [
                'bail',
                'nullable',
                new RequireWithButWithout('mobile_number', 'sim'),
                'prohibits:sim',
                'integer',
                new ExistAndAuthorized('activeSenderIds', 'sender_ids.id')
            ]
        ];
    }
}
