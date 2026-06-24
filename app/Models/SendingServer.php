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

use App\Jobs\SendCampaign;
use App\Observers\SendingServerObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use App\Contracts\Campaignable as CampaignableContract;

#[ObservedBy([SendingServerObserver::class])]
/**
 * @property int $id
 * @property string $name
 * @property string $driver
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $config
 * @property array<array-key, mixed> $supported_types
 * @property bool $enabled
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Campaignable|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Quota|null $quota
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SenderId> $senderIds
 * @property-read int|null $sender_ids_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\SendingServerFactory factory($count = null, $state = [])
 * @method static Builder<static>|SendingServer newModelQuery()
 * @method static Builder<static>|SendingServer newQuery()
 * @method static Builder<static>|SendingServer query()
 * @method static Builder<static>|SendingServer whereConfig($value)
 * @method static Builder<static>|SendingServer whereCreatedAt($value)
 * @method static Builder<static>|SendingServer whereDriver($value)
 * @method static Builder<static>|SendingServer whereEnabled($value)
 * @method static Builder<static>|SendingServer whereId($value)
 * @method static Builder<static>|SendingServer whereName($value)
 * @method static Builder<static>|SendingServer whereSupportedTypes($value)
 * @method static Builder<static>|SendingServer whereUpdatedAt($value)
 * @method static Builder<static>|SendingServer whereUserId($value)
 * @method static Builder<static>|SendingServer withConfig()
 * @mixin \Eloquent
 */
class SendingServer extends Model implements CampaignableContract
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'config' => SchemalessAttributes::class,
            'supported_types' => 'array',
            'enabled' => 'boolean',
            'user_id' => 'integer',
        ];
    }

    public function quota(): MorphOne
    {
        return $this->morphOne(Quota::class, 'quotable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaigns(): MorphToMany
    {
        return $this->morphToMany(Campaign::class, 'campaignable')
                    ->using(Campaignable::class)
                    ->withPivot('senders', 'resume_at', 'status');
    }

    public function senderIds(): HasMany
    {
        return $this->hasMany(SenderId::class);
    }

    public function messages(): HasManyThrough
    {
        return $this->hasManyThrough(Message::class, SenderId::class, 'sending_server_id', 'messageable_id')
                    ->whereMorphedTo('messageable', SenderId::class);
    }

    public function scopeWithConfig(): Builder
    {
        return $this->config->modelScope();
    }

    public function send(Campaign $campaign): void
    {
        SendCampaign::dispatch($campaign, $this);
    }
}
