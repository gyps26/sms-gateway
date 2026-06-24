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

use App\Enums\MessageType;
use App\Models\Campaign;
use App\Rules\HasSubscribers;
use App\Rules\Delay;
use App\Rules\MobileNumber;
use App\Rules\ExistAndAuthorized;
use App\Rules\MmsAttachmentUrl;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

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
                new ExistAndAuthorized('activeSims', 'sims.id')
            ],
            'sims.*' => ['distinct'],
            'sender_ids' => [
                'bail',
                'required_without:sims',
                'array',
                new ExistAndAuthorized('activeSenderIds', 'sender_ids.id')
            ],
            'sender_ids.*' => ['distinct'],
            'random_sender' => ['sometimes', 'boolean'],
            'contact_lists' => [
                'bail',
                'required_without:mobile_numbers',
                'prohibits:mobile_numbers',
                'array',
            ],
            'contact_lists.*' => ['bail', 'distinct', 'integer', new HasSubscribers()],
            'mobile_numbers' => [
                'required_without:contact_lists',
                'prohibits:contact_lists',
                'array',
                'min:1',
            ],
            'mobile_numbers.*' => [new MobileNumber()],
            'type' => ['sometimes', new Enum(MessageType::class)],
            'message' => [
                'sometimes',
                'required_if:type,SMS',
                'required_without:attachments',
                'string'
            ],
            'attachments' => ['sometimes', 'prohibited_unless:type,MMS', 'array'],
            'attachments.*' => ['distinct', new MmsAttachmentUrl()],
            'delivery_report' => ['sometimes', 'prohibited_unless:type,SMS', 'boolean'],
            'delay' => ['sometimes', Rule::prohibitedIf(fn() => empty($this->input('sims'))), new Delay()],
            'prioritize' => ['sometimes', Rule::prohibitedIf(fn() => empty($this->input('sims'))), 'boolean'],
            ...parent::rules()
        ];
    }

    public function messages(): array
    {
        return [
            'delay.prohibited' => __('validation.custom.prohibited_without', ['attribute' => __('validation.attributes.delay'), 'values' => __('validation.attributes.sims')]),
            'prioritize.prohibited' => __('validation.custom.prohibited_without', ['attribute' => __('validation.attributes.prioritize'), 'values' => __('validation.attributes.sims')]),
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'sims.*' => [
                'description' => 'The ids of the sims to use for sending the messages. You can also set the first element to * and it will use all available sims. This field is required when sender_ids is not present.',
                'example' => 1,
            ],
            'sender_ids.*' => [
                'description' => 'The ids of the sender ids to use for sending the messages. You can also set the first element to * and it will use all available sender ids. This field is required when sims is not present.',
                'example' => 1,
            ],
            'random_sender' => [
                'description' => 'Whether to use a random sim or sender id from selected sims or sender_ids for entire campaign.',
                'example' => true,
            ],
            'contact_lists.*' => [
                'description' => 'The ids of the contact lists to send the messages. This field is required when mobile_numbers is not present. This field is prohibited when mobile_numbers is present.',
                'example' => 1,
            ],
            'mobile_numbers.*' => [
                'description' => 'The mobile numbers to send the messages. This field is required when contact_lists is not present. This field is prohibited when contact_lists is present.',
                'example' => '+12345678901',
            ],
            'type' => [
                'description' => 'The type of the message.',
                'example' => 'SMS',
            ],
            'message' => [
                'description' => 'The message to send.',
                'example' => 'Hello, World!',
            ],
            'attachments.*' => [
                'description' => 'The array of file URLs you want to send as attachments with the message. This field is prohibited when type is not MMS.',
                'example' => 'https://example.com/image.jpg',
            ],
            'delivery_report' => [
                'description' => 'Whether to request a delivery report for the message. This field is prohibited when type is not SMS.',
                'example' => true,
            ],
            'delay' => [
                'description' => 'The delay between messages in seconds. This field is prohibited when sims is not present.',
                'example' => 60,
            ],
            'prioritize' => [
                'description' => 'Whether to prioritize the campaign. This field is prohibited when sims is not present.',
                'example' => true,
            ],
            ...parent::bodyParameters()
        ];
    }
}
