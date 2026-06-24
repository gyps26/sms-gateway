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

use App\Enums\CampaignableStatus;
use App\Models\Campaignable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\SendingServer */
class CampaignSendingServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the sending server.')]
    #[ResponseField(name: 'name', type: 'string', description: 'The name of the sending server.')]
    #[ResponseField(name: 'status', type: 'string', description: 'The status of the campaign on this sending server.', enum: CampaignableStatus::class)]
    #[ResponseField(name: 'sender_ids', type: 'object[]', description: 'The sender ids of the sending server that are used in the campaign.')]
    #[ResponseField(name: 'sender_ids.*.id', type: 'integer', description: 'The ID of the sender id.')]
    #[ResponseField(name: 'sender_ids.*.value', type: 'string', description: 'The value of the sender id.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enabled' => $this->enabled,
            'status' => $this->whenPivotLoaded(new Campaignable, fn() => $this->pivot->status),
            'sender_ids' => SenderIdResource::collection($this->whenLoaded('senderIds')),
        ];
    }
}
