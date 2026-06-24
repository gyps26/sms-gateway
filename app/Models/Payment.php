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

use App\Data\Filters\PaymentFiltersData;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

/**
 * @property int $id
 * @property string $amount
 * @property CurrencyAlpha3 $currency
 * @property string $transaction_id
 * @property int $quantity
 * @property PaymentStatus $status
 * @property int $subscription_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Subscription $subscription
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Payment filter(\App\Data\Filters\PaymentFiltersData $filters)
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment whereAmount($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereCurrency($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereQuantity($value)
 * @method static Builder<static>|Payment whereStatus($value)
 * @method static Builder<static>|Payment whereSubscriptionId($value)
 * @method static Builder<static>|Payment whereTransactionId($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'subscription_id' => 'integer',
            'status' => PaymentStatus::class,
            'currency' => CurrencyAlpha3::class
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function scopeFilter(Builder $query, PaymentFiltersData $filters): void
    {
        $query->when($filters->status, fn($query, $status) => $query->where('status', $status))
              ->when($filters->subscription, fn($query, $subscription) => $query->where('subscription_id', $subscription))
              ->when($filters->user, function ($query, $user) {
                  return $query->whereHas('subscription', fn($query) => $query->where('user_id', $user));
              })
              ->when($filters->method, function ($query, $method) {
                  return $query->whereHas('subscription', fn($query) => $query->where('payment_method', $method));
              });
    }
}
