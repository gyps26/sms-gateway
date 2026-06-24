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

use App\Data\Filters\UssdPullFiltersData;
use App\Enums\UssdPullStatus;
use App\Models\Scopes\DateRange;
use App\Observers\UssdPullObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

#[ObservedBy(UssdPullObserver::class)]
/**
 * @property int $id
 * @property string|null $from
 * @property string $code
 * @property UssdPullStatus $status
 * @property string|null $response
 * @property int $user_id
 * @property int $campaign_id
 * @property int $sim_id
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $received_at
 * @property-read \App\Models\Campaign $campaign
 * @property-read \App\Models\Device|null $device
 * @property-read \App\Models\Sim $sim
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\UssdPullFactory factory($count = null, $state = [])
 * @method static Builder<static>|UssdPull filter(\App\Data\Filters\UssdPullFiltersData $filters)
 * @method static Builder<static>|UssdPull newModelQuery()
 * @method static Builder<static>|UssdPull newQuery()
 * @method static Builder<static>|UssdPull query()
 * @method static Builder<static>|UssdPull search(string $search)
 * @method static Builder<static>|UssdPull whereCampaignId($value)
 * @method static Builder<static>|UssdPull whereCode($value)
 * @method static Builder<static>|UssdPull whereFrom($value)
 * @method static Builder<static>|UssdPull whereId($value)
 * @method static Builder<static>|UssdPull whereReceivedAt($value)
 * @method static Builder<static>|UssdPull whereResponse($value)
 * @method static Builder<static>|UssdPull whereSentAt($value)
 * @method static Builder<static>|UssdPull whereSimId($value)
 * @method static Builder<static>|UssdPull whereStatus($value)
 * @method static Builder<static>|UssdPull whereUserId($value)
 * @mixin \Eloquent
 */
class UssdPull extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'campaign_id' => 'integer',
            'sim_id' => 'integer',
            'user_id' => 'integer',
            'sent_at' => 'datetime',
            'received_at' => 'datetime',
            'status' => UssdPullStatus::class
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sim(): BelongsTo
    {
        return $this->belongsTo(Sim::class);
    }

    public function device(): HasOneThrough
    {
        return $this->hasOneThrough(Device::class, Sim::class);
    }

    public function scopeFilter(Builder $query, UssdPullFiltersData $filters): void
    {
        $query->when($filters->campaign, fn($query, $campaign) => $query->where('campaign_id', $campaign))
              ->when($filters->user, fn($query, $user) => $query->where('user_id', $user))
              ->when($filters->sim, fn($query, $sim) => $query->where('sim_id', $sim))
              ->when($filters->statuses, fn($query, $statuses) => $query->where('status', $statuses))
              ->tap(new DateRange($filters->after, $filters->before));
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where('ussd_pulls.response', 'like', "%{$search}%")
              ->orWhere('ussd_pulls.code', $search);
    }
}
