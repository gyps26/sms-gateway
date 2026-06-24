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

use App\Data\Filters\SubscriptionFiltersData;
use App\Enums\SubscriptionStatus;
use App\Observers\SubscriptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;

#[ObservedBy(SubscriptionObserver::class)]
/**
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property \Illuminate\Support\Carbon|null $renewal_at
 * @property SubscriptionStatus $status
 * @property string|null $payment_method
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $billing_info
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $taxes
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $features
 * @property string $subscription_id
 * @property int|null $coupon_id
 * @property int $plan_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Coupon|null $coupon
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read \App\Models\Plan $plan
 * @property-read \App\Models\User $user
 * @method static Builder<static>|Subscription active()
 * @method static \Database\Factories\SubscriptionFactory factory($count = null, $state = [])
 * @method static Builder<static>|Subscription filter(\App\Data\Filters\SubscriptionFiltersData $filters)
 * @method static Builder<static>|Subscription newModelQuery()
 * @method static Builder<static>|Subscription newQuery()
 * @method static Builder<static>|Subscription query()
 * @method static Builder<static>|Subscription whereBillingInfo($value)
 * @method static Builder<static>|Subscription whereCouponId($value)
 * @method static Builder<static>|Subscription whereCreatedAt($value)
 * @method static Builder<static>|Subscription whereEndsAt($value)
 * @method static Builder<static>|Subscription whereFeatures($value)
 * @method static Builder<static>|Subscription whereId($value)
 * @method static Builder<static>|Subscription wherePaymentMethod($value)
 * @method static Builder<static>|Subscription wherePlanId($value)
 * @method static Builder<static>|Subscription whereRenewalAt($value)
 * @method static Builder<static>|Subscription whereStatus($value)
 * @method static Builder<static>|Subscription whereSubscriptionId($value)
 * @method static Builder<static>|Subscription whereTaxes($value)
 * @method static Builder<static>|Subscription whereUpdatedAt($value)
 * @method static Builder<static>|Subscription whereUserId($value)
 * @method static Builder<static>|Subscription withBillingInfo()
 * @method static Builder<static>|Subscription withFeatures()
 * @method static Builder<static>|Subscription withTaxes()
 * @mixin \Eloquent
 */
class Subscription extends Model
{
    use HasFactory;
    use SchemalessAttributesTrait;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'coupon_id' => 'integer',
            'plan_id' => 'integer',
            'user_id' => 'integer',
            'status' => SubscriptionStatus::class,
            'billing_info' => SchemalessAttributes::class,
            'taxes' => SchemalessAttributes::class,
            'features' => SchemalessAttributes::class,
            'subscription_id' => 'string',
            'ends_at' => 'datetime',
            'renewal_at' => 'datetime',
        ];
    }

    public function scopeWithBillingInfo(): Builder
    {
        return $this->billing_info->modelScope();
    }

    public function scopeWithTaxes(): Builder
    {
        return $this->taxes->modelScope();
    }

    public function scopeWithFeatures(): Builder
    {
        return $this->features->modelScope();
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeFilter(Builder $query, SubscriptionFiltersData $filters): void
    {
        $query->when($filters->status, fn($query, $status) => $query->where('status', $status))
              ->when($filters->plan, fn($query, $plan) => $query->where('plan_id', $plan))
              ->when($filters->user, fn($query, $user) => $query->where('user_id', $user));
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('status', SubscriptionStatus::Active);
    }

    public function renew(): void
    {
        $renewalAt = now()->add($this->plan->interval_unit->value, $this->plan->interval);

        $this->update([
            'renewal_at' => $renewalAt >= $this->ends_at ? null : $renewalAt,
        ]);
    }

    public function cancel(bool $immediate = false): void
    {
        if ($this->status === SubscriptionStatus::Active && isset($this->payment_method)) {
            $class = PaymentGateway::find(Str::slug($this->payment_method))->class;

            /** @var \App\Contracts\PaymentGateway $gateway */
            $gateway = new $class();
            $gateway->cancelSubscription($this->subscription_id);
        }

        $this->update([
            'status' => SubscriptionStatus::Cancelled,
            'renewal_at' => null,
            'ends_at' => $immediate ? now() : ($this->renewal_at ?? $this->ends_at)
        ]);
    }
}
