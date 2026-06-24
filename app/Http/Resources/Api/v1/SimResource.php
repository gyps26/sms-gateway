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

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\Sim */
class SimResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'Unique identifier for the SIM.')]
    #[ResponseField(name: 'name', type: 'string', description: 'The name of the SIM.')]
    #[ResponseField(name: 'label', type: 'string', description: 'The label of the SIM.')]
    #[ResponseField(name: 'number', type: 'string', description: 'The number of the SIM.')]
    #[ResponseField(name: 'country', type: 'string', description: 'The country of the SIM.')]
    #[ResponseField(name: 'carrier', type: 'string', description: 'The carrier of the SIM.')]
    #[ResponseField(name: 'slot', type: 'integer', description: 'The slot of the SIM.')]
    #[ResponseField(name: 'active', type: 'boolean', description: 'Whether the SIM is active or not.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->getRawOriginal('label'),
            'number' => $this->number,
            'country' => $this->country,
            'carrier' => $this->carrier,
            'slot' => $this->slot,
            'data_roaming' => $this->data_roaming,
            'signal_strength' => $this->signal_strength,
            'active' => $this->active
        ];
    }
}
