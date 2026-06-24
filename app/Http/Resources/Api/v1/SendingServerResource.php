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

use App\Models\Campaignable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\SendingServer */
class SendingServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the sending server.')]
    #[ResponseField(name: 'name', type: 'string', description: 'The name of the sending server.')]
    #[ResponseField(name: 'driver', type: 'string', description: 'The driver of the sending server.')]
    #[ResponseField(name: 'supported_types', type: 'string[]', description: 'The types of messages supported by the sending server.')]
    #[ResponseField(name: 'config', type: 'object', description: 'The sending server configuration.')]
    #[ResponseField(name: 'enabled', type: 'boolean', description: 'Whether the sending server is enabled or not.')]
    #[ResponseField(name: 'created_at', type: 'string', description: 'The date and time when the sending server was created.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'driver' => $this->driver,
            'supported_types' => $this->supported_types,
            'config' => $this->config,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
        ];
    }
}
