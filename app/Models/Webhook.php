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

use App\Enums\WebhookEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $url
 * @property string|null $secret
 * @property \Illuminate\Support\Collection<int, WebhookEvent> $events
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WebhookCall> $webhookCalls
 * @property-read int|null $webhook_calls_count
 * @method static \Database\Factories\WebhookFactory factory($count = null, $state = [])
 * @method static Builder<static>|Webhook forEvent(\App\Enums\WebhookEvent $event)
 * @method static Builder<static>|Webhook newModelQuery()
 * @method static Builder<static>|Webhook newQuery()
 * @method static Builder<static>|Webhook query()
 * @method static Builder<static>|Webhook whereCreatedAt($value)
 * @method static Builder<static>|Webhook whereEvents($value)
 * @method static Builder<static>|Webhook whereId($value)
 * @method static Builder<static>|Webhook whereSecret($value)
 * @method static Builder<static>|Webhook whereUpdatedAt($value)
 * @method static Builder<static>|Webhook whereUrl($value)
 * @method static Builder<static>|Webhook whereUserId($value)
 * @mixin \Eloquent
 */
class Webhook extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'events' => AsEnumCollection::class.':'.WebhookEvent::class
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function webhookCalls(): HasMany
    {
        return $this->hasMany(WebhookCall::class);
    }

    public function scopeForEvent(Builder $query, WebhookEvent $event): void
    {
        $query->whereJsonContains('events', $event->value);
    }

    /**
     * @param  array<int, string>  $except
     */
    public function call(WebhookEvent $event, Model $model, array $except = []): void
    {
        $payload = [
            'event' => $event,
            'data' => $model->except($except)
        ];

        $webhookCall = WebhookCall::create([
            'webhook_id' => $this->id,
            'event' => $event,
            'resource_id' => $model->getKey(),
            'resource_type' => get_class($model),
            'payload' => $payload
        ]);

        $credits = config('saas.credits.webhook_call.amount');

        if ($this->user->consume($credits)) {
            $webhookCall->send();
        } else {
            $webhookCall->update([
                'status' => 'Failed',
                'response' => __('messages.global.limit_exceeded')
            ]);
        }
    }
}
