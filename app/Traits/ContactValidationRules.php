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

use App\Models\Contact;
use App\Models\ContactList;
use App\Rules\MobileNumber;
use Illuminate\Validation\Rule;

trait ContactValidationRules
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function contactRules(ContactList $contactList, ?Contact $contact = null): array
    {
        return [
            'mobile_number' => [
                'required',
                new MobileNumber(),
                Rule::unique('contacts', 'mobile_number')
                    ->where('contact_list_id', $contactList->id)
                    ->ignore($contact)
            ],
            'subscribed' => ['required', 'boolean'],
            ...$contactList->fieldsValidationRules()
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'mobile_number' => [
                'description' => 'The mobile number of the contact.',
                'example' => '+12025886500',
            ],
            'subscribed' => [
                'description' => 'Whether the contact is subscribed to the contact list.',
                'example' => true,
            ],
            'first_name' => [
                'description' => 'The first name of the contact. This is a custom field so if you didn\'t set it up, it won\'t be available.',
                'example' => 'John',
            ],
            'last_name' => [
                'description' => 'The last name of the contact. This is a custom field so if you didn\'t set it up, it won\'t be available.',
                'example' => 'Doe',
            ],
        ];
    }
}
