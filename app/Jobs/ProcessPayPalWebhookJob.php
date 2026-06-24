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
use App\PaymentGateways\PayPal;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessPayPalWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $payload = $this->webhookCall->payload;

        if (data_get($payload, 'event_type') === 'BILLING.SUBSCRIPTION.CREATED') {
            if (Subscription::whereSubscriptionId(data_get($payload, 'resource.id'))->exists()) {
                return;
            }

            $metadata = Cache::get(data_get($payload, 'resource.custom_id'));

            if ($metadata && Arr::has($metadata, ['user_id', 'plan_id', 'billing_info', 'taxes', 'coupon_id'])) {
                Subscription::create([
                    'user_id' => $metadata['user_id'],
                    'plan_id' => $metadata['plan_id'],
                    'coupon_id' => $metadata['coupon_id'],
                    'payment_method' => PayPal::label(),
                    'billing_info' => $metadata['billing_info'],
                    'taxes' => $metadata['taxes'],
                    'subscription_id' => data_get($payload, 'resource.id'),
                    'status' => SubscriptionStatus::Pending,
                    'renewal_at' => Carbon::make(data_get($payload, 'resource.billing_info.next_billing_time')),
                ]);
            } else {
                Log::error('Metadata not found for ' . data_get($payload, 'resource.custom_id'));
            }
        }

        if (Str::startsWith(data_get($payload, 'event_type'), 'BILLING.SUBSCRIPTION.')) {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'resource.id'))->first();

            if (is_null($subscription) || $subscription->status === SubscriptionStatus::Cancelled) {
                return;
            }

            if ($subscription->status === SubscriptionStatus::Pending && data_get($payload, 'event_type') === 'BILLING.SUBSCRIPTION.ACTIVATED') {
                $metadata = Cache::get(data_get($payload, 'resource.custom_id'));

                if (isset($metadata['coupon_id'])) {
                    Coupon::whereId($metadata['coupon_id'])->increment('redeemed');
                }
            }

            $status = match (data_get($payload, 'resource.status')) {
                'ACTIVE' => SubscriptionStatus::Active,
                'SUSPENDED' => SubscriptionStatus::Suspended,
                'CANCELLED' => SubscriptionStatus::Cancelled,
                'EXPIRED' => SubscriptionStatus::Expired,
                default => SubscriptionStatus::Pending,
            };

            $renewalAt = data_get($payload, 'resource.billing_info.next_billing_time')
                ? Carbon::make(data_get($payload, 'resource.billing_info.next_billing_time'))
                : null;

            $endsAt = match ($status) {
                SubscriptionStatus::Cancelled => $subscription->renewal_at,
                SubscriptionStatus::Expired, SubscriptionStatus::Suspended => now(),
                default => null,
            };

            $subscription->update([
                'status' => $status,
                'renewal_at' => $renewalAt,
                'ends_at' => $endsAt,
            ]);
        } elseif (data_get($payload, 'event_type') === 'PAYMENT.SALE.COMPLETED') {
            if (Payment::whereTransactionId(data_get($payload, 'resource.id'))->exists()) {
                return;
            }

            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'resource.billing_agreement_id'))
                                        ->first();

            if (is_null($subscription)) {
                return;
            }

            $paypal = new PayPal();
            $paypalSubscription = $paypal->getSubscription($subscription->subscription_id);

            $subscription->update([
                'renewal_at' => Carbon::make(data_get($paypalSubscription, 'billing_info.next_billing_time')),
            ]);

            Payment::create([
                'subscription_id' => $subscription->id,
                'transaction_id' => data_get($payload, 'resource.id'),
                'amount' => data_get($payload, 'resource.amount.total'),
                'currency' => data_get($payload, 'resource.amount.currency'),
                'status' => PaymentStatus::Completed,
            ]);
        } elseif (data_get($payload, 'event_type') === 'PAYMENT.SALE.REFUNDED') {
            $payment = Payment::whereTransactionId(data_get($payload, 'resource.sale_id'))->first();

            if (is_null($payment)) {
                return;
            }

            if ($payment->amount === data_get($payload, 'resource.total_refunded_amount.value')) {
                $payment->update([
                    'status' => PaymentStatus::Refunded
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::PartiallyRefunded,
                    'amount' => $payment->amount - data_get($payload, 'resource.total_refunded_amount.value')
                ]);
            }
        } elseif (data_get($payload, 'event_type') === 'PAYMENT.SALE.REVERSED') {
            $payment = Payment::whereTransactionId(data_get($payload, 'resource.id'))->first();

            if (is_null($payment)) {
                return;
            }

            $payment->update(['status' => PaymentStatus::Reversed]);
        }
    }
}
