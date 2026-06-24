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

use App\Models\Campaign;
use App\Traits\SendUssdPullsValidationRules;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class SendUssdPullsRequest extends SendCampaignRequest
{
    use SendUssdPullsValidationRules;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Campaign::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            ...$this->sendUssdPullsRules(),
            ...parent::rules(),
            'random_sender' => ['sometimes', 'boolean'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'sims.*' => [
                'description' => 'The ids of the sims to use for sending the USSD pulls. You can also set the first element to * and it will use all available sims.',
                'example' => 1,
            ],
            'random_sender' => [
                'description' => 'Whether to use a random sim from selected sims for entire campaign.',
                'example' => true,
            ],
            'ussd_codes.*' => [
                'description' => 'The array of USSD codes to send.',
                'example' => '*123#',
            ],
            'delay' => [
                'description' => 'The delay in seconds between each USSD pull.',
                'example' => 60,
            ],
            'prioritize' => [
                'description' => 'Whether to prioritize the campaign.',
                'example' => true,
            ],
            ...parent::bodyParameters()
        ];
    }
}
