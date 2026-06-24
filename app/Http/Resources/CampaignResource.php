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

/** @mixin \App\Models\Campaign */
class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => $this->label,
            'type' => $this->type,
            'status' => $this->status,
            'scheduled_at' => $this->scheduled_at,
            'timezone' => $this->timezone,
            'days_of_week' => $this->days_of_week,
            'recurring' => $this->recurring,
            'ends_at' => $this->ends_at,
            'payload' => $this->payload,
            'active_hours' => $this->active_hours,
            'frequency' => $this->frequency,
            'frequency_unit' => $this->frequency_unit,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'devices_count' => $this->whenCounted('devices'),
            'sending_servers_count' => $this->whenCounted('sendingServers'),
        ];
    }
}
