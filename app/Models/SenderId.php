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

use App\Contracts\Messageable;
use App\Observers\SenderIdObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[ObservedBy(SenderIdObserver::class)]
/**
 * @property int $id
 * @property string $value
 * @property int $sending_server_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SenderIdUser> $senderIdUsers
 * @property-read int|null $sender_id_users_count
 * @property-read \App\Models\SendingServer $sendingServer
 * @property-read \App\Models\SenderIdUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static Builder<static>|SenderId active()
 * @method static \Database\Factories\SenderIdFactory factory($count = null, $state = [])
 * @method static Builder<static>|SenderId newModelQuery()
 * @method static Builder<static>|SenderId newQuery()
 * @method static Builder<static>|SenderId query()
 * @method static Builder<static>|SenderId whereCreatedAt($value)
 * @method static Builder<static>|SenderId whereId($value)
 * @method static Builder<static>|SenderId whereSendingServerId($value)
 * @method static Builder<static>|SenderId whereUpdatedAt($value)
 * @method static Builder<static>|SenderId whereValue($value)
 * @mixin \Eloquent
 */
class SenderId extends Model implements Messageable
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'sending_server_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function sendingServer(): BelongsTo
    {
        return $this->belongsTo(SendingServer::class);
    }

    public function senderIdUsers(): HasMany
    {
        return $this->hasMany(SenderIdUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(SenderIdUser::class)->withTimestamps();
    }

    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function scopeActive(Builder $query): void
    {
        $query->whereHas('sendingServer', fn($query) => $query->whereEnabled(true));
    }

    public function fromAddress(): ?string
    {
        return $this->value;
    }
}
