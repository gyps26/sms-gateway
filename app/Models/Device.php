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

use App\Contracts\Campaignable as CampaignableContract;
use App\Contracts\JwtSubject;
use App\Jobs\SendPushNotification;
use App\Observers\DeviceObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy([DeviceObserver::class])]
/**
 * @property int $id
 * @property string|null $name
 * @property int $battery
 * @property bool $is_charging
 * @property string $model
 * @property string $android_id
 * @property string $android_version
 * @property int $sdk_version
 * @property string $app_version
 * @property int $app_version_code
 * @property string|null $logs
 * @property string|null $firebase_token
 * @property bool $enabled
 * @property int $owner_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeviceUser|\App\Models\Campaignable|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DeviceUser> $deviceUsers
 * @property-read int|null $device_users_count
 * @property-read mixed $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \App\Models\User $owner
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sim> $sims
 * @property-read int|null $sims_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UssdPull> $ussdPulls
 * @property-read int|null $ussd_pulls_count
 * @method static \Database\Factories\DeviceFactory factory($count = null, $state = [])
 * @method static Builder<static>|Device newModelQuery()
 * @method static Builder<static>|Device newQuery()
 * @method static Builder<static>|Device query()
 * @method static Builder<static>|Device search(string $search)
 * @method static Builder<static>|Device whereAndroidId($value)
 * @method static Builder<static>|Device whereAndroidVersion($value)
 * @method static Builder<static>|Device whereAppVersion($value)
 * @method static Builder<static>|Device whereAppVersionCode($value)
 * @method static Builder<static>|Device whereBattery($value)
 * @method static Builder<static>|Device whereCreatedAt($value)
 * @method static Builder<static>|Device whereEnabled($value)
 * @method static Builder<static>|Device whereFirebaseToken($value)
 * @method static Builder<static>|Device whereId($value)
 * @method static Builder<static>|Device whereIsCharging($value)
 * @method static Builder<static>|Device whereLogs($value)
 * @method static Builder<static>|Device whereModel($value)
 * @method static Builder<static>|Device whereName($value)
 * @method static Builder<static>|Device whereOwnerId($value)
 * @method static Builder<static>|Device whereSdkVersion($value)
 * @method static Builder<static>|Device whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Device extends Model implements CampaignableContract, JwtSubject
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = ['label'];

    protected function casts(): array
    {
        return [
            'sdk_version' => 'integer',
            'app_version_code' => 'integer',
            'battery' => 'integer',
            'owner_id' => 'integer',
            'is_charging' => 'boolean',
            'enabled' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->using(DeviceUser::class);
    }

    public function deviceUsers(): HasMany
    {
        return $this->hasMany(DeviceUser::class);
    }

    public function sims(): HasMany
    {
        return $this->hasMany(Sim::class);
    }

    public function messages(): HasManyThrough
    {
        return $this->hasManyThrough(Message::class, Sim::class, 'device_id', 'messageable_id')
                    ->whereMorphedTo('messageable', Sim::class);
    }

    public function ussdPulls(): HasManyThrough
    {
        return $this->hasManyThrough(UssdPull::class, Sim::class);
    }

    public function campaigns(): MorphToMany
    {
        return $this->morphToMany(Campaign::class, 'campaignable')
                    ->using(Campaignable::class)->withPivot(['senders', 'status', 'resume_at']);
    }

    protected function label(): Attribute
    {
        return Attribute::make(get: fn() => ($this->name ?? $this->model) . " [$this->id]");
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->whereAny(['devices.name', 'devices.model'], 'like', "%{$search}%");
    }

    public function updateOrCreateSims(array $sims): void
    {
        foreach ($sims as $sim) {
            Sim::updateOrCreate(['slot' => $sim['slot'], 'device_id' => $this->id], $sim);
        }
    }

    public function send(Campaign $campaign): void
    {
        SendPushNotification::dispatch($campaign, $this);
    }

    public function getJwtIdentifier(): mixed
    {
        return $this->getKey();
    }
}
