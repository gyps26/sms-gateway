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

namespace App\PaymentGateways;

use App\Contracts\PaymentGateway;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Helpers\Calculate;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class BankTransfer implements PaymentGateway
{

    public static function label(): string
    {
        return 'Bank Transfer';
    }

    public static function fields(): array
    {
        return [
            'instructions' => [
                'label' => 'Instructions',
                'type' => 'textarea'
            ],
        ];
    }

    public function initPayment(
        Plan $plan,
        Collection $taxes,
        ?Coupon $coupon,
        array $billingInfo,
        int $quantity = 1
    ): RedirectResponse {
        $amount = Calculate::amount($plan->price, $quantity, $taxes, $coupon?->discount);

        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $plan->id,
            'coupon_id' => $coupon?->id,
            'payment_method' => self::label(),
            'subscription_id' => Str::uuid(),
            'billing_info' => $billingInfo,
            'taxes' => $taxes->toArray(),
            'status' => $amount > 0 ? SubscriptionStatus::Pending : SubscriptionStatus::Active,
            'renewal_at' => $quantity > 1 ? now()->add($plan->interval_unit->value, $plan->interval) : null,
            'ends_at' => now()->add($plan->interval_unit->value, $plan->interval * $quantity)
        ]);

        if ($subscription->status === SubscriptionStatus::Active) {
            return Redirect::route('subscribe')->with('success', __('messages.subscription.started'));
        }

        $payment = Payment::create([
            'amount' => round($amount, $plan->currency->getMinorUnits()),
            'currency' => $plan->currency->value,
            'transaction_id' => Str::uuid(),
            'quantity' => $quantity,
            'status' => PaymentStatus::Pending,
            'subscription_id' => $subscription->id,
        ]);

        return Redirect::to(route('payments.show', $payment->id));
    }

    public function handleWebhook(Request $request): Response
    {
        abort(404);
    }

    public function cancelSubscription(string $id): void
    {
        //
    }

    public function callback(Request $request): RedirectResponse
    {
        return Redirect::to('subscribe');
    }
}
