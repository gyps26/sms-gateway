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

/** @mixin \App\Models\Campaign */
class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the campaign.')]
    #[ResponseField(name: 'name', type: 'string', description: 'The name of the campaign.', nullable: true)]
    #[ResponseField(name: 'scheduled_at', type: 'string', description: 'The date and time when the campaign is scheduled to run.', nullable: true)]
    #[ResponseField(name: 'timezone', type: 'string', description: 'The timezone of the campaign.')]
    #[ResponseField(name: 'recurring', type: 'boolean', description: 'Whether the campaign is recurring.')]
    #[ResponseField(name: 'frequency', type: 'integer', description: 'The frequency of the campaign.', nullable: true)]
    #[ResponseField(name: 'frequency_unit', type: 'string', description: 'The unit of the frequency.', nullable: true)]
    #[ResponseField(name: 'ends_at', type: 'string', description: 'The date and time when the recurring campaign ends.', nullable: true)]
    #[ResponseField(name: 'repeat_at', type: 'string', description: 'The date and time when the campaign is scheduled to repeat.', nullable: true)]
    #[ResponseField(name: 'active_hours', type: 'string', description: 'The timespan of a day when the campaign is active.')]
    #[ResponseField(name: 'days_of_week', type: 'integer[]', description: 'The days of the week when the campaign is active.')]
    #[ResponseField(name: 'status', type: 'string', description: 'The status of the campaign.')]
    #[ResponseField(name: 'type', type: 'string', description: 'The type of the campaign.')]
    #[ResponseField(name: 'options', type: 'object', description: 'The options of the campaign.')]
    #[ResponseField(name: 'options.delay', type: 'string', description: 'The delay between each item before sending the next one.', required: false)]
    #[ResponseField(name: 'options.prioritize', type: 'boolean', description: 'Whether to prioritize the campaign.', required: false)]
    #[ResponseField(name: 'options.delivery_report', type: 'boolean', description: 'Whether to request delivery report for SMS.', required: false)]
    #[ResponseField(name: 'created_at', type: 'string', description: 'The date and time the campaign was created.')]
    #[ResponseField(name: 'updated_at', type: 'string', description: 'The date and time the campaign was last updated.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'scheduled_at' => $this->scheduled_at,
            'timezone' => $this->timezone,
            'recurring' => $this->recurring,
            'frequency' => $this->frequency,
            'frequency_unit' => $this->frequency_unit,
            'ends_at' => $this->ends_at,
            'repeat_at' => $this->repeat_at,
            'active_hours' => $this->active_hours,
            'days_of_week' => $this->days_of_week,
            'status' => $this->status,
            'type' => $this->type,
            'options' => $this->options,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
