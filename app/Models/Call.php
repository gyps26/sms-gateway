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

use App\Data\Filters\CallFiltersData;
use App\Enums\CallType;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $number
 * @property CallType $type
 * @property \Illuminate\Support\Carbon $started_at
 * @property int $duration
 * @property int $user_id
 * @property int $sim_id
 * @property-read \App\Models\Sim $sim
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\CallFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call filter(\App\Data\Filters\CallFiltersData $filters)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call search(string $search)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereSimId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereStartedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Call whereUserId($value)
 * @mixin \Eloquent
 */
class Call extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => CallType::class,
            'started_at' => 'datetime',
            'duration' => 'integer',
            'user_id' => 'integer',
            'sim_id' => 'integer',
        ];
    }

    public function sim(): BelongsTo
    {
        return $this->belongsTo(Sim::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->whereAny(['calls.number'], 'LIKE', "%{$search}%", 'or');
    }

    public function scopeFilter(Builder $query, CallFiltersData $filters): void
    {
        $query->when($filters->type, fn($query, $type) => $query->where('calls.type', $type))
              ->when($filters->sim, fn($query, $sim) => $query->where('calls.sim_id', $sim))
              ->when(isset($filters->answered), function ($query) use ($filters) {
                  $subQuery = function ($query) {
                      $query->select('number')
                            ->from('calls')
                            ->whereIn('type', [CallType::Outgoing, CallType::Incoming]);
                  };

                  $query->whereIn('type', [CallType::Missed, CallType::Voicemail, CallType::Rejected]);
                  if ($filters->answered) {
                      $query->whereIn('number', $subQuery);
                  } else {
                      $query->whereNotIn('number', $subQuery);
                  }
              });
    }
}
