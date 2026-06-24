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

namespace App\Http\Controllers;

use App\Data\Filters\PaymentFiltersData;
use App\DataTable;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Http\Resources\PaymentResource;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(PaymentFiltersData $filters): InertiaResponse|RedirectResponse
    {
        $users = function () {
            $users = Auth::user()->is_admin ? User::all(['id', 'name', 'email']) : new Collection();

            return $users->keyBy('id');
        };

        return DataTable::make(Payment::filter($filters)->with('subscription'))
                        ->search(function ($query, $search) {
                            $query->where('payments.transaction_id', $search);
                        })
                        ->sort(['amount', 'currency', 'subscription_id', 'transaction_id', 'method', 'status', 'created_at', 'user_id'])
                        ->render('Payments/Index', fn($data) => [
                            'payments' => fn() => PaymentResource::collection($data()),
                            'users' => $users,
                            'paymentMethods' => fn() => PaymentGateway::all()->pluck('label'),
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment): InertiaResponse
    {
        Gate::authorize('view', $payment);

        return Inertia::render('Payments/Show', [
            'payment' => $payment->load(['subscription.plan', 'subscription.coupon']),
            'instructions' => config('payment-gateways.BankTransfer.instructions'),
        ]);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function approve(Payment $payment): RedirectResponse
    {
        Gate::authorize('approve', $payment);

        $payment->update(['status' => PaymentStatus::Completed]);

        $plan = $payment->subscription->plan;

        $endsAt = now()->add($plan->interval_unit->value, $plan->interval * $payment->quantity);

        $payment->subscription->update([
            'status' => SubscriptionStatus::Active,
            'renewal_at' => $endsAt,
            'ends_at' => $endsAt
        ]);

        if ($couponId = $payment->subscription->coupon_id) {
            Coupon::whereId($couponId)->increment('redeemed');
        }

        return Redirect::back()->with('success', __('messages.payment.approved'));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function decline(Payment $payment): RedirectResponse
    {
        Gate::authorize('decline', $payment);

        $payment->subscription()->delete();

        return Redirect::back()->with('success', __('messages.payment.declined'));
    }

    public function invoice(Payment $payment): InertiaResponse
    {
        Gate::authorize('view', $payment);

        return Inertia::render('Payments/Invoice', [
            'payment' => $payment->load(['subscription.plan', 'subscription.coupon']),
            'billingInfo' => nl2br(config('saas.billing_info')),
        ]);
    }
}
