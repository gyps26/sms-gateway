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

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\Call */
class CallResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the call.')]
    #[ResponseField(name: 'number', type: 'string', description: 'The phone number of the call.')]
    #[ResponseField(name: 'type', type: 'string', description: 'The type of the call.')]
    #[ResponseField(name: 'sim_id', type: 'integer', description: 'The ID of the SIM used for the call.')]
    #[ResponseField(name: 'started_at', type: 'string', description: 'The time when the call was started.')]
    #[ResponseField(name: 'duration', type: 'integer', description: 'The duration of the call in seconds.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'number' => $this->number,
            'type' => $this->type,
            'sim_id' => $this->sim_id,
            'started_at' => $this->started_at,
            'duration' => $this->duration
        ];
    }
}
