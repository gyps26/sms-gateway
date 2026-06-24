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

use App\Enums\MessageType;
use App\Models\Campaign;
use App\Rules\HasSubscribers;
use App\Rules\Delay;
use App\Rules\ExistAndAuthorized;
use App\Rules\MobileNumber;
use App\Rules\Spreadsheet;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\File;

class SendMessagesRequest extends SendCampaignRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool|Response
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
            'sims' => [
                'bail',
                'required_without:sender_ids',
                'array',
                new ExistAndAuthorized('activeSims', 'sims.id'),
            ],
            'sims.*' => ['distinct'],
            'sender_ids' => [
                'bail',
                'required_without:sims',
                'array',
                new ExistAndAuthorized('activeSenderIds', 'sender_ids.id'),
            ],
            'sender_ids.*' => ['distinct'],
            'recipients' => ['required', Rule::in(['mobile_numbers', 'spreadsheet', 'contact_lists'])],
            'mobile_numbers' => [
                'exclude_unless:recipients,mobile_numbers',
                'required_if:recipients,mobile_numbers',
                'array'
            ],
            'mobile_numbers.*' => ['distinct', new MobileNumber()],
            'spreadsheet' => [
                'bail',
                'exclude_unless:recipients,spreadsheet',
                'required_if:recipients,spreadsheet',
                'file',
                new Spreadsheet(),
            ],
            'contact_lists' => [
                'exclude_unless:recipients,contact_lists',
                'required_if:recipients,contact_lists',
                'array'
            ],
            'contact_lists.*' => [
                'bail',
                'distinct',
                'required',
                'integer',
                'min:1',
                new HasSubscribers()
            ],
            'type' => ['required', new Enum(MessageType::class)],
            'message' => [
                'nullable',
                'required_if:type,SMS',
                'required_without:attachments',
                'string'
            ],
            'attachments' => [
                'exclude_if:type,SMS',
                'nullable',
                'array',
                'max:10'
            ],
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
                    'vcf',
                    'html'
                ])->max(400)
            ],
            'delivery_report' => [
                'exclude_unless:type,SMS',
                'exclude_without:sims',
                'nullable',
                'required_if:type,SMS',
                'required_with:sims',
                'boolean'
            ],
            'delay' => ['exclude_without:sims', 'nullable', 'required_with:sims', new Delay()],
            'prioritize' => ['exclude_without:sims', 'nullable', 'required_with:sims', 'boolean'],
            ...parent::rules()
        ];
    }
}
