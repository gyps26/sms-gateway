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

use App\Enums\UssdPullStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\UssdPull */
class UssdPullResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the USSD pull.')]
    #[ResponseField(name: 'code', type: 'string', description: 'The USSD code.')]
    #[ResponseField(name: 'response', type: 'string', description: 'The response from the USSD pull.')]
    #[ResponseField(name: 'status', type: 'string', description: 'The status of the USSD pull.', enum: UssdPullStatus::class)]
    #[ResponseField(name: 'sim_id', type: 'integer', description: 'The id of the sim used to send the USSD pull.')]
    #[ResponseField(name: 'sent_at', type: 'string', description: 'The date and time when the USSD pull was sent.')]
    #[ResponseField(name: 'received_at', type: 'string', description: 'The date and time when the response from the USSD pull was received.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'response' => $this->response,
            'status' => $this->status,
            'sim_id' => $this->sim_id,
            'sent_at' => $this->sent_at,
            'received_at' => $this->received_at,
        ];
    }
}
