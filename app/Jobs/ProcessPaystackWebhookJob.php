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
use App\PaymentGateways\Paystack;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessPaystackWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function handle(): void
    {
        $payload = $this->webhookCall->payload;

        if (data_get($payload, 'event') === 'subscription.create') {
            if (Subscription::whereSubscriptionId(data_get($payload, 'data.subscription_code'))->exists()) {
                return;
            }

            $metadata = json_decode(data_get($payload, 'data.plan.description'), true);

            if ($metadata && Arr::has($metadata, ['user_id', 'plan_id', 'billing_info', 'taxes', 'coupon_id'])) {
                Subscription::create([
                    'user_id' => $metadata['user_id'],
                    'plan_id' => $metadata['plan_id'],
                    'billing_info' => $metadata['billing_info'] + ['plan_code' => data_get($payload, 'data.plan.plan_code')],
                    'taxes' => $metadata['taxes'],
                    'payment_method' => Paystack::label(),
                    'subscription_id' => data_get($payload, 'data.subscription_code'),
                    'coupon_id' => $metadata['coupon_id'],
                    'status' => SubscriptionStatus::Active,
                    'renewal_at' => Carbon::make(data_get($payload, 'data.next_payment_date')),
                ]);

                if (isset($metadata['coupon_id'])) {
                    Coupon::whereId($metadata['coupon_id'])->increment('redeemed');
                }
            } else {
                Log::error('Metadata not found for ' . data_get($payload, 'data.plan.plan_code'));
            }
        } elseif (Str::startsWith(data_get($payload, 'event'), 'subscription.')) {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'data.subscription_code'))
                                        ->first();

            if (is_null($subscription) || $subscription->status === SubscriptionStatus::Cancelled) {
                return;
            }

            $status = match (data_get($payload, 'data.status')) {
                'active' => SubscriptionStatus::Active,
                'attention' => SubscriptionStatus::Suspended,
                'complete' => SubscriptionStatus::Expired,
                'cancelled', 'non-renewing' => SubscriptionStatus::Cancelled,
                default => SubscriptionStatus::Pending,
            };

            $endsAt = null;
            $renewalAt = null;
            if (data_get($payload, 'data.next_payment_date')) {
                $renewalAt = Carbon::make(data_get($payload, 'data.next_payment_date'));
            } else {
                $endsAt = match ($status) {
                    SubscriptionStatus::Cancelled, SubscriptionStatus::Suspended => $subscription->renewal_at,
                    SubscriptionStatus::Expired => Carbon::now(),
                    default => null,
                };
            }

            $subscription->update([
                'status' => $status,
                'renewal_at' => $renewalAt,
                'ends_at' => $endsAt
            ]);
        } elseif (data_get($payload, 'event') === 'charge.success') {
            if (Payment::whereTransactionId(data_get($payload, 'data.reference'))->exists()) {
                return;
            }

            $subscription = Subscription::whereJsonContains('billing_info->plan_code',
                data_get($payload, 'data.plan.plan_code'))->first();

            if (is_null($subscription)) {
                $paystack = new Paystack();
                $paystackSubscription = Arr::first($paystack->listSubscriptions(data_get($payload, 'data.plan.id')));

                if (is_null($paystackSubscription)) {
                    return;
                }

                $metadata = json_decode(data_get($paystackSubscription, 'plan.description'), true);

                if ($metadata && Arr::has($metadata, ['user_id', 'plan_id', 'billing_info', 'taxes', 'coupon_id'])) {
                    $subscription = Subscription::create([
                        'user_id' => $metadata['user_id'],
                        'plan_id' => $metadata['plan_id'],
                        'billing_info' => $metadata['billing_info'] + ['plan_code' => data_get($paystackSubscription, 'plan.plan_code')],
                        'taxes' => $metadata['taxes'],
                        'payment_method' => Paystack::label(),
                        'subscription_id' => data_get($paystackSubscription, 'subscription_code'),
                        'coupon_id' => $metadata['coupon_id'],
                        'status' => SubscriptionStatus::Active,
                        'renewal_at' => Carbon::make(data_get($paystackSubscription, 'next_payment_date')),
                    ]);

                    if (isset($metadata['coupon_id'])) {
                        Coupon::whereId($metadata['coupon_id'])->increment('redeemed');
                    }
                } else {
                    Log::error('Metadata not found for ' . data_get($paystackSubscription, 'plan.plan_code'));
                }
            } else {
                $paystack = new Paystack();
                $paystackSubscription = $paystack->getSubscription($subscription->subscription_id);

                $subscription->update([
                    'status' => SubscriptionStatus::Active,
                    'renewal_at' => Carbon::make(data_get($paystackSubscription, 'data.next_payment_date'))
                ]);
            }

            if (is_null($subscription)) {
                return;
            }

            Payment::create([
                'amount' => data_get($payload, 'data.amount') / 100,
                'currency' => data_get($payload, 'data.currency'),
                'transaction_id' => data_get($payload, 'data.reference'),
                'status' => PaymentStatus::Completed,
                'subscription_id' => $subscription->id,
            ]);
        } elseif (data_get($payload, 'event') === 'refund.processed') {
            $payment = Payment::whereTransactionId(data_get($payload, 'data.transaction_reference'))->first();

            if (is_null($payment)) {
                return;
            }

            if (data_get($payload, 'data.amount') === $payment->amount * 100) {
                $payment->update([
                    'status' => PaymentStatus::Refunded
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::PartiallyRefunded,
                    'amount' => $payment->amount - data_get($payload, 'data.amount') / 100,
                ]);
            }
        } elseif (data_get($payload, 'event') === 'invoice.payment_failed') {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'data.subscription.subscription_code'))->first();

            if (is_null($subscription)) {
                return;
            }

            $subscription->update([
                'status' => SubscriptionStatus::Suspended
            ]);
        }
    }
}
