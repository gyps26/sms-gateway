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

use App\Enums\MessageStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Knuckles\Scribe\Attributes\ResponseField;

/** @mixin \App\Models\Message */
class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ResponseField(name: 'id', type: 'integer', description: 'The ID of the message.')]
    #[ResponseField(name: 'from', type: 'string', description: 'The sender of the message.')]
    #[ResponseField(name: 'to', type: 'string', description: 'The recipient of the message.')]
    #[ResponseField(name: 'phone_id', type: 'string', description: 'The identifier (usually a name) linked to the sender or receiver’s phone number, as defined in the contact list.')]
    #[ResponseField(name: 'content', type: 'string', description: 'The content of the message.')]
    #[ResponseField(name: 'type', type: 'string', description: 'The type of the message.')]
    #[ResponseField(name: 'attachments', type: 'string[]', description: 'List of all attachments for the message.')]
    #[ResponseField(name: 'status', type: 'string', description: 'The status of the message.', enum: MessageStatus::class)]
    #[ResponseField(name: 'response', type: 'object', description: 'The response received from the device or sending server.')]
    #[ResponseField(name: 'messenger', type: 'string', description: 'The sim or sender id used for the message.')]
    #[ResponseField(name: 'sent_at', type: 'string', description: 'The date and time when the message was sent.')]
    #[ResponseField(name: 'delivered_at', type: 'string', description: 'The date and time when the message was delivered.')]
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'phone_id' => $this->phone_id,
            'content' => $this->content,
            'type' => $this->type,
            'attachments' => MediaResource::collection($this->getMedia('attachments') ? $this->getMedia('attachments') : $this->campaign->getMedia('attachments')),
            'status' => $this->status,
            'response' => $this->whenNotNull($this->response),
            'messenger' => sprintf("%s #%s", Str::headline($this->messageable_type), $this->messageable_id),
            'sent_at' => $this->sent_at,
            'delivered_at' => $this->delivered_at,
        ];
    }
}
