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

use App\Enums\IntervalUnit;
use App\Observers\PlanObserver;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

#[ObservedBy(PlanObserver::class)]
/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $price
 * @property CurrencyAlpha3 $currency
 * @property int $interval
 * @property IntervalUnit $interval_unit
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $features
 * @property int $position
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Database\Factories\PlanFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereFeatures($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereInterval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereIntervalUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Plan withFeaturesAttributes()
 * @mixin \Eloquent
 */
class Plan extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'interval' => 'integer',
            'position' => 'integer',
            'currency' => CurrencyAlpha3::class,
            'interval_unit' => IntervalUnit::class,
            'features' => SchemalessAttributes::class,
            'enabled' => 'boolean',
        ];
    }

    public function scopeWithFeaturesAttributes(): Builder
    {
        return $this->features->modelScope();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    protected function label(): Attribute
    {
        return Attribute::make(function () {
            $price = round($this->price, $this->currency->getMinorUnits());
            return "{$this->name} ({$price} {$this->currency->value})";
        });
    }

    public function criteria(User $user): Collection
    {
        $counts = $user->loadCount([
            'contacts as contacts',
            'contactLists as contact_lists',
            'ownedDevices as devices',
            'sendingServers as sending_servers',
            'ownedSenderIds as sender_ids',
            'templates as templates',
            'webhooks as webhooks',
            'tokens as api_tokens',
            'autoResponses as auto_responses'
        ])->only([
            'contacts',
            'contact_lists',
            'devices',
            'sending_servers',
            'sender_ids',
            'templates',
            'webhooks',
            'api_tokens',
            'auto_responses'
        ]);

        $additional = new Collection();
        foreach ($counts as $feature => $usage) {
            $limit = $this->features->{$feature};
            if (is_null($limit) || $usage <= $limit) {
                $additional->put($feature, 0);
            } else {
                $additional->put($feature, $usage - $limit);
            }
        }

        return $additional;
    }
}
