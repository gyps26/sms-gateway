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

namespace App\Models;

use App\Data\Filters\WebhookCallFiltersData;
use App\Enums\WebhookCallStatus;
use App\Enums\WebhookEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\WebhookServer\WebhookCall as SpatieWebhookCall;

/**
 * @property int $id
 * @property WebhookEvent $event
 * @property array<array-key, mixed> $payload
 * @property string|null $response
 * @property int|null $status_code
 * @property int $resource_id
 * @property string $resource_type
 * @property int $attempts
 * @property \Illuminate\Support\Carbon|null $last_retry_at
 * @property WebhookCallStatus $status
 * @property int $webhook_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $resource
 * @property-read \App\Models\Webhook $webhook
 * @method static \Database\Factories\WebhookCallFactory factory($count = null, $state = [])
 * @method static Builder<static>|WebhookCall filter(\App\Data\Filters\WebhookCallFiltersData $filters)
 * @method static Builder<static>|WebhookCall newModelQuery()
 * @method static Builder<static>|WebhookCall newQuery()
 * @method static Builder<static>|WebhookCall query()
 * @method static Builder<static>|WebhookCall whereAttempts($value)
 * @method static Builder<static>|WebhookCall whereCreatedAt($value)
 * @method static Builder<static>|WebhookCall whereEvent($value)
 * @method static Builder<static>|WebhookCall whereId($value)
 * @method static Builder<static>|WebhookCall whereLastRetryAt($value)
 * @method static Builder<static>|WebhookCall wherePayload($value)
 * @method static Builder<static>|WebhookCall whereResourceId($value)
 * @method static Builder<static>|WebhookCall whereResourceType($value)
 * @method static Builder<static>|WebhookCall whereResponse($value)
 * @method static Builder<static>|WebhookCall whereStatus($value)
 * @method static Builder<static>|WebhookCall whereStatusCode($value)
 * @method static Builder<static>|WebhookCall whereUpdatedAt($value)
 * @method static Builder<static>|WebhookCall whereWebhookId($value)
 * @mixin \Eloquent
 */
class WebhookCall extends Model
{
    use HasFactory;
    use MassPrunable;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'last_retry_at' => 'datetime',
            'status_code' => 'integer',
            'attempts' => 'integer',
            'resource_id' => 'integer',
            'webhook_id' => 'integer',
            'event' => WebhookEvent::class,
            'status' => WebhookCallStatus::class
        ];
    }

    public function resource(): MorphTo
    {
        return $this->morphTo();
    }

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }

    public function prunable(): Builder
    {
        return static::query()->where('created_at', '<=', now()->subDay())->whereNotIn('status', ['Failed', 'Permanently Failed']);
    }

    public function scopeFilter(Builder $query, WebhookCallFiltersData $filters): void
    {
        $query->when($filters->status, fn($query, $status) => $query->where('status', $status))
              ->when($filters->event, fn($query, $event) => $query->where('event', $event));
    }

    public function send(): void
    {
        $call = SpatieWebhookCall::create()
                                 ->url($this->webhook->url)
                                 ->meta(['id' => $this->id])
                                 ->payload($this->payload);

        if ($this->webhook->secret) {
            $call->useSecret($this->webhook->secret);
        } else {
            $call->doNotSign();
        }

        $call->dispatch();
    }

    public function resend(): void
    {
        $this->update([
            'status' => 'Pending',
            'attempts' => 0,
            'last_retry_at' => null,
            'response' => null,
            'status_code' => null,
        ]);

        $this->send();
    }
}
