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
use App\PaymentGateways\Stripe;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Stripe\Event;
use Spatie\WebhookClient\Jobs\ProcessWebhookJob;

class ProcessStripeWebhookJob extends ProcessWebhookJob
{

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $event = Event::constructFrom($this->webhookCall->payload);

        if ($event->type === 'customer.subscription.created') {
            $resource = $event->data->object;

            if (Subscription::whereSubscriptionId($resource->id)->exists()) {
                return;
            }

            if ($resource->metadata->user_id && $resource->metadata->plan_id && $resource->metadata->billing_info && $resource->metadata->taxes) {
                Subscription::create([
                    'user_id' => $resource->metadata->user_id,
                    'plan_id' => $resource->metadata->plan_id,
                    'coupon_id' => $resource->metadata->coupon_id,
                    'payment_method' => Stripe::label(),
                    'billing_info' => json_decode($resource->metadata->billing_info, true),
                    'taxes' => json_decode($resource->metadata->taxes, true),
                    'subscription_id' => $resource->id,
                    'status' => SubscriptionStatus::Pending,
                    'renewal_at' => null,
                    'ends_at' => null,
                ]);

                if (isset($resource->metadata->coupon_id)) {
                    Coupon::whereId($resource->metadata->coupon_id)->increment('redeemed');
                }
            }
        } elseif (Str::startsWith($event->type, 'customer.subscription.')) {
            $resource = $event->data->object;

            $subscription = Subscription::whereSubscriptionId($resource->id)->first();

            if (is_null($subscription) || $subscription->status === SubscriptionStatus::Cancelled) {
                return;
            }

            $status = match ($resource->status) {
                'active' => SubscriptionStatus::Active,
                'unpaid', 'past_due' => SubscriptionStatus::Suspended,
                'canceled' => SubscriptionStatus::Cancelled,
                default => SubscriptionStatus::Pending,
            };

            $endsAt = null;
            if ($resource->cancel_at_period_end) {
                $endsAt = Carbon::createFromTimestamp($resource->current_period_end);
            } elseif ($resource->cancel_at) {
                $endsAt = Carbon::createFromTimestamp($resource->cancel_at);
            } elseif ($resource->canceled_at) {
                $endsAt = Carbon::createFromTimestamp($resource->canceled_at);
            }

            $renewalAt = is_null($endsAt) && $resource->current_period_end
                ? Carbon::createFromTimestamp($resource->current_period_end) : null;

            $subscription->update([
                'status' => $status,
                'renewal_at' => $renewalAt,
                'ends_at' => $endsAt,
            ]);
        } elseif ($event->type === 'invoice.paid') {
            $resource = $event->data->object;

            if (Payment::whereTransactionId($resource->charge)->exists()) {
                return;
            }

            $subscription = Subscription::whereSubscriptionId($resource->subscription)->first();

            if (is_null($subscription)) {
                return;
            }

            $currency = Str::upper($resource->currency);

            Payment::create([
                'amount' => $this->getAmount($resource->amount_paid, $currency),
                'currency' => $currency,
                'transaction_id' => $resource->charge,
                'status' => PaymentStatus::Completed,
                'subscription_id' => $subscription->id,
            ]);
        } elseif (Str::startsWith($event->type, 'charge.refunded')) {
            $resource = $event->data->object;

            $payment = Payment::whereTransactionId($resource->id)->first();

            if (is_null($payment)) {
                return;
            }

            $currency = Str::upper($resource->currency);

            $amountRefunded = $this->getAmount($resource->amount_refunded, $currency);
            if ($payment->amount === $amountRefunded) {
                $payment->update([
                    'status' => PaymentStatus::Refunded
                ]);
            } else {
                $payment->update([
                    'status' => PaymentStatus::PartiallyRefunded,
                    'amount' => $payment->amount - $amountRefunded
                ]);
            }
        }
    }

    public static function getAmount($amount, $currency): float
    {
        if (in_array($currency, Stripe::ZERO_DECIMAL_CURRENCIES)) {
            return $amount;
        } else {
            return $amount / 100;
        }
    }
}
