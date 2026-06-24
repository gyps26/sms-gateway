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

/** @mixin \App\Models\Device */
class DeviceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the device.')]
    #[ResponseField(name: 'name', type: 'string', description: 'The name of the device.', nullable: true)]
    #[ResponseField(name: 'model', type: 'string', description: 'The model of the device.')]
    #[ResponseField(name: 'enabled', type: 'boolean', description: 'Whether the user has logged in to the app on the device.')]
    #[ResponseField(name: 'android_version', type: 'string', description: 'The android version of the device.')]
    #[ResponseField(name: 'app_version', type: 'string', description: 'The app version installed on the device.')]
    #[ResponseField(name: 'battery', type: 'integer', description: 'The percentage of battery remaining on the device.')]
    #[ResponseField(name: 'is_charging', type: 'boolean', description: 'Whether the device is charging or not.')]
    #[ResponseField(name: 'updated_at', type: 'datetime', description: 'The last time the device was updated.')]
    #[ResponseField(name: 'sims', type: 'object', description: 'List of all sims on the device.')]
    #[ResponseField(name: 'sims.*.id', type: 'integer', description: 'The ID of the sim.')]
    #[ResponseField(name: 'sims.*.name', type: 'string', description: 'The name of the sim.')]
    #[ResponseField(name: 'sims.*.label', type: 'string', description: 'The label of the sim.')]
    #[ResponseField(name: 'sims.*.number', type: 'string', description: 'The number of the sim.')]
    #[ResponseField(name: 'sims.*.country', type: 'string', description: 'The country of the sim.')]
    #[ResponseField(name: 'sims.*.carrier', type: 'string', description: 'The carrier of the sim.')]
    #[ResponseField(name: 'sims.*.slot', type: 'integer', description: 'The slot of the sim.')]
    #[ResponseField(name: 'sims.*.active', type: 'boolean', description: 'Whether the sim is active or not.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'model' => $this->model,
            'enabled' => $this->enabled,
            'android_version' => $this->android_version,
            'app_version' => $this->app_version,
            'battery' => $this->battery,
            'is_charging' => $this->is_charging,
            'updated_at' => $this->updated_at,
            'sims' => SimResource::collection($this->whenLoaded('sims')),
        ];
    }
}
