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
use App\PaymentGateways\Razorpay;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessRazorpayWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->webhookCall->payload;

        if (Str::startsWith(data_get($payload, 'event'), 'subscription.')) {
            $subscription = Subscription::whereSubscriptionId(data_get($payload, 'payload.subscription.entity.id'))
                                        ->first();

            $status = match (data_get($payload, 'payload.subscription.entity.status')) {
                'active', 'resumed' => SubscriptionStatus::Active,
                'pending', 'halted', 'paused' => SubscriptionStatus::Suspended,
                'expired', 'completed' => SubscriptionStatus::Expired,
                'cancelled' => SubscriptionStatus::Cancelled,
                default => SubscriptionStatus::Pending,
            };

            $renewalAt = data_get($payload, 'payload.subscription.entity.charge_at')
                ? Carbon::createFromTimestamp(data_get($payload, 'payload.subscription.entity.charge_at')) : null;

            $endsAt = data_get($payload, 'payload.subscription.entity.ended_at')
                ? Carbon::createFromTimestamp(data_get($payload, 'payload.subscription.entity.ended_at')) : null;

            if ($subscription) {
                if ($subscription->status === SubscriptionStatus::Cancelled) {
                    return;
                }

                $subscription->update([
                    'status' => $status,
                    'renewal_at' => $renewalAt,
                    'ends_at' => $endsAt
                ]);

                if (is_null(data_get($payload, 'payload.payment'))) {
                    return;
                }

                if (Payment::whereTransactionId(data_get($payload, 'payload.payment.entity.id'))->exists()) {
                    return;
                }

                $amount = data_get($payload, 'payload.payment.entity.amount');
                $currency = data_get($payload, 'payload.payment.entity.currency');

                Payment::create([
                    'amount' => $this->getAmount($amount, $currency),
                    'currency' => $currency,
                    'transaction_id' => data_get($payload, 'payload.payment.entity.id'),
                    'status' => PaymentStatus::Completed,
                    'subscription_id' => $subscription->id,
                ]);
            } else {
                $notes = data_get($payload, 'payload.subscription.entity.notes');

                $metadata = Cache::get(data_get($notes, 'cache_id'));

                if ($metadata && Arr::has($metadata, ['user_id', 'plan_id', 'billing_info', 'taxes'])) {
                    Subscription::create([
                        'user_id' => $metadata['user_id'],
                        'plan_id' => $metadata['plan_id'],
                        'billing_info' => $metadata['billing_info'],
                        'taxes' => $metadata['taxes'],
                        'subscription_id' => data_get($payload, 'payload.subscription.entity.id'),
                        'payment_method' => Razorpay::label(),
                        'coupon_id' => $metadata['coupon_id'],
                        'status' => $status,
                        'renewal_at' => $renewalAt,
                        'ends_at' => $endsAt,
                    ]);

                    if (isset($metadata['coupon_id'])) {
                        Coupon::whereId($metadata['coupon_id'])->increment('redeemed');
                    }
                } else {
                    Log::error('Metadata not found for ' . data_get($payload, 'data.plan.plan_code'));
                }
            }
        }

        if (data_get($payload, 'event') === 'refund.processed') {
            $payment = Payment::whereTransactionId(data_get($payload, 'payload.refund.entity.payment_id'))->first();

            if (is_null($payment)) {
                return;
            }

            $refundAmount = $this->getAmount(data_get($payload, 'payload.refund.entity.amount'), $payment->currency);
            if ($payment->amount === $refundAmount) {
                $payment->update(['status' => PaymentStatus::Refunded]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::PartiallyRefunded,
                    'amount' => $payment->amount - $refundAmount
                ]);
            }
        }
    }

    private function getAmount($amount, $currency): float|int
    {
        $result = $amount;
        if (in_array($currency, Razorpay::TWO_DECIMAL_CURRENCIES)) {
            $result = $amount / 100;
        } else if (in_array($currency, Razorpay::THREE_DECIMAL_CURRENCIES)) {
            $result = $amount / 1000;
        }
        return $result;
    }
}
