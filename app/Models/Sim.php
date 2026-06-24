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
use App\Observers\SimObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

#[ObservedBy([SimObserver::class])]
/**
 * @property int $id
 * @property string|null $label
 * @property string|null $name
 * @property string|null $number
 * @property string|null $country
 * @property string|null $carrier
 * @property string|null $icc_id
 * @property string|null $mcc
 * @property string|null $mnc
 * @property bool|null $data_roaming
 * @property int|null $signal_strength
 * @property int $slot
 * @property bool $active
 * @property int $device_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Call> $calls
 * @property-read int|null $calls_count
 * @property-read \App\Models\Device $device
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\Quota|null $quota
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UssdPull> $ussdPulls
 * @property-read int|null $ussd_pulls_count
 * @method static \Database\Factories\SimFactory factory($count = null, $state = [])
 * @method static Builder<static>|Sim newModelQuery()
 * @method static Builder<static>|Sim newQuery()
 * @method static Builder<static>|Sim query()
 * @method static Builder<static>|Sim search(string $search)
 * @method static Builder<static>|Sim whereActive($value)
 * @method static Builder<static>|Sim whereCarrier($value)
 * @method static Builder<static>|Sim whereCountry($value)
 * @method static Builder<static>|Sim whereCreatedAt($value)
 * @method static Builder<static>|Sim whereDataRoaming($value)
 * @method static Builder<static>|Sim whereDeviceId($value)
 * @method static Builder<static>|Sim whereIccId($value)
 * @method static Builder<static>|Sim whereId($value)
 * @method static Builder<static>|Sim whereLabel($value)
 * @method static Builder<static>|Sim whereMcc($value)
 * @method static Builder<static>|Sim whereMnc($value)
 * @method static Builder<static>|Sim whereName($value)
 * @method static Builder<static>|Sim whereNumber($value)
 * @method static Builder<static>|Sim whereSignalStrength($value)
 * @method static Builder<static>|Sim whereSlot($value)
 * @method static Builder<static>|Sim whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sim extends Model implements Messageable
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'data_roaming' => 'boolean',
            'slot' => 'integer',
            'signal_strength' => 'integer',
            'device_id' => 'integer',
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function quota(): MorphOne
    {
        return $this->morphOne(Quota::class, 'quotable');
    }

    public function messages(): MorphMany
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    public function ussdPulls(): HasMany
    {
        return $this->hasMany(UssdPull::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    protected function label(): Attribute
    {
        return Attribute::make(
            get: function (?string $value) {
                if ($value) {
                    return $value;
                }

                $label = $this->number ?? $this->name ?? $this->carrier;
                $name = "SIM #{$this->id}";
                $name .= transform($label, fn($label) => " ($label)", '');
                $device = $this->relationLoaded('device') ? $this->device->label : null;
                return $device ? "$device | $name" :  $name;
            },
        );
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->whereAny(['sims.name', 'sims.label', 'sims.number'], 'like', "%{$search}%", 'or')
              ->orWhere('sims.id', $search);
    }

    public function fromAddress(): ?string
    {
        return $this->number ?? $this->getRawOriginal('label');
    }
}
