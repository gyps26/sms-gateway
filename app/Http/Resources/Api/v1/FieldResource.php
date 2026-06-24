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

use App\Enums\FieldType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\Field */
class FieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the field.')]
    #[ResponseField(name: 'label', type: 'string', description: 'The label of the field.')]
    #[ResponseField(name: 'tag', type: 'string', description: 'The tag of the field.')]
    #[ResponseField(name: 'type', type: 'string', description: 'The type of the field.', enum: FieldType::class)]
    #[ResponseField(name: 'options', type: 'object[]', description: 'List of all possible options for the field.')]
    #[ResponseField(name: 'options.*.label', type: 'string', description: 'The label of the option.')]
    #[ResponseField(name: 'options.*.value', type: 'string', description: 'The value of the option.')]
    #[ResponseField(name: 'default_value', type: 'string', description: 'The default value of the field.')]
    #[ResponseField(name: 'required', type: 'boolean', description: 'Whether the field is required or not.')]
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'label' => $this->label,
            'tag' => $this->tag,
            'type' => $this->type,
            'options' => $this->options,
            'default_value' => $this->default_value,
            'required' => $this->required
        ];
    }
}
