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

namespace App\Jobs;

use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Subscription;
use App\PaymentGateways\CryptoCom;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessCryptoComWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     *
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $payload = $this->webhookCall->payload;

        if (data_get($payload, 'type') === 'subscription.activated') {
            if (Subscription::whereSubscriptionId(data_get($payload, 'data.object.id'))->exists()) {
                return;
            }

            $metadata = data_get($payload, 'data.object.metadata');

            if ($metadata && Arr::has($metadata, ['user_id', 'plan_id', 'billing_info', 'taxes'])) {
                Subscription::create([
                    'user_id' => $metadata['user_id'],
                    'plan_id' => $metadata['plan_id'],
                    'billing_info' => $metadata['billing_info'],
                    'taxes' => $metadata['taxes'],
                    'payment_method' => CryptoCom::label(),
                    'subscription_id' => data_get($payload, 'data.object.id'),
                    'coupon_id' => $metadata['coupon_id'] ?? null,
                    'status' => SubscriptionStatus::Active,
                    'renewal_at' => Carbon::createFromTimestamp(data_get($payload, 'data.object.current_period_end')),
                ]);

                if (isset($metadata['coupon_id'])) {
                    Coupon::whereId($metadata['coupon_id'])->increment('redeemed');
                }
            } else {
                Log::error('Metadata not found for ' . data_get($payload, 'data.object.id'));
            }
        } elseif (data_get($payload, 'type') === 'subscription.cancelled') {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'data.object.id'))->first();

            if (is_null($subscription) || $subscription->status === SubscriptionStatus::Cancelled) {
                return;
            }

            if ($subscription->status === SubscriptionStatus::Cancelled) {
                return;
            }

            $subscription->update([
                'status' => SubscriptionStatus::Cancelled,
                'renewal_at' => null,
                'ends_at' => $subscription->renewal_at
            ]);
        } elseif (data_get($payload, 'type') === 'invoice.paid') {
            if (Payment::whereTransactionId(data_get($payload, 'data.object.id'))->exists()) {
                return;
            }

            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'data.object.subscription_id'))
                                        ->first();

            if (is_null($subscription)) {
                return;
            }

            $cryptoCom = new CryptoCom();
            $cryptoComSubscription = $cryptoCom->getSubscription($subscription->subscription_id);

            $subscription->update([
                'status' => SubscriptionStatus::Active,
                'renewal_at' => Carbon::createFromTimestamp(data_get($cryptoComSubscription, 'current_period_end'))
            ]);

            Payment::create([
                'amount' => data_get($payload, 'data.object.amount'),
                'currency' => data_get($payload, 'data.object.currency'),
                'transaction_id' => data_get($payload, 'data.object.id'),
                'status' => PaymentStatus::Completed,
                'subscription_id' => $subscription->id
            ]);
        } elseif (data_get($payload, 'type') === 'invoice.payment_failed') {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'data.object.subscription_id'))
                                        ->first();

            if (is_null($subscription)) {
                return;
            }

            $subscription->update([
                'status' => SubscriptionStatus::Suspended
            ]);
        }
    }
}
