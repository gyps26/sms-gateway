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

namespace App\Http\Resources\Api\v1;

use App\Http\Resources\ContactFieldResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\Contact */
class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the contact.')]
    #[ResponseField(name: 'mobile_number', type: 'string', description: 'The mobile number of the contact.')]
    #[ResponseField(name: 'first_name', type: 'string', description: 'The first name of the contact. This is a custom field, so if you didn\'t set it up, then it won\'t be available.')]
    #[ResponseField(name: 'last_name', type: 'string', description: 'The last name of the contact.  This is a custom field, so if you didn\'t set it up, then it won\'t be available.', nullable: true)]
    #[ResponseField(name: 'subscribed', type: 'boolean', description: 'Whether the contact is subscribed or not.')]
    #[ResponseField(name: 'created_at', type: 'string', description: 'The date and time when the contact was created.')]
    #[ResponseField(name: 'updated_at', type: 'string', description: 'The date and time when the contact was last updated.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'mobile_number' => $this->mobile_number,
            ...$this->extras,
            'subscribed' => $this->subscribed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
