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

use App\Data\AssignSubscriptionData;
use App\Data\CancelSubscriptionData;
use App\Data\Filters\SubscriptionFiltersData;
use App\DataTable;
use App\Enums\SubscriptionStatus;
use App\Http\Requests\CheckoutPlanRequest;
use App\Http\Requests\StoreSubscriptionRequest;
use App\Http\Requests\UpdateSubscriptionRequest;
use App\Http\Resources\CountryFilterResource;
use App\Http\Resources\SubscriptionResource;
use App\Models\Coupon;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tax;
use App\Models\User;
use App\PaymentGateways\BankTransfer;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use PrinsFrank\Standards\Country\CountryAlpha2;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class SubscriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(SubscriptionFiltersData $filters): Response|RedirectResponse
    {
        $users = function () {
            $users = Auth::user()->is_admin ? User::all(['id', 'name', 'email']) : new Collection();

            return $users->keyBy('id');
        };

        $plans = function () {
            $plans = Plan::all(['id', 'name', 'price', 'currency'])->append('label');

            return $plans->keyBy('id');
        };

        return
            DataTable::make(Subscription::query()->filter($filters))
                     ->search(function ($query, $search) {
                         $query->where('subscriptions.subscription_id', $search);
                     })
                     ->sort(['plan_id', 'status', 'recurring', 'payment_method', 'renewal_at', 'created_at', 'user_id'])
                     ->render('Subscriptions/Index', fn($data) => [
                         'subscriptions' => fn() => SubscriptionResource::collection($data()),
                         'params' => $filters->toArray(),
                         'users' => $users,
                         'plans' => $plans
                     ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSubscriptionRequest $request, Plan $plan): BaseResponse
    {
        $paymentMethod = Str::slug($request->input('payment_method'));
        $class = PaymentGateway::find($paymentMethod)->class;

        /** @var \App\Contracts\PaymentGateway $paymentGateway */
        $paymentGateway = new $class();

        $taxes = Tax::whereEnabled(true)
                    ->whereCountry($request->input('country'))
                    ->get(['name', 'rate', 'inclusive']);

        $coupon = transform($request->input('coupon'), fn($code) => Coupon::whereCode($code)->first());
        $billingInfo = $request->only([
            'first_name',
            'last_name',
            'company',
            'address_line_1',
            'address_line_2',
            'city',
            'state',
            'country',
            'postal_code',
            'phone'
        ]);
        $quantity = $request->integer('quantity');

        return $paymentGateway->initPayment($plan, $taxes, $coupon, $billingInfo, $quantity);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSubscriptionRequest $request, Subscription $subscription): RedirectResponse
    {
        $subscription->features->credits = $request->validated('features.credits');
        $subscription->save();

        return Redirect::back()->with('success', __('messages.subscription.updated'));
    }

    public function checkout(CheckoutPlanRequest $request, Plan $plan): Response
    {
        $paymentMethods = PaymentGateway::all()
                                        ->filter(fn($paymentGateway) => $paymentGateway->enabled)
                                        ->pluck('label');

        return Inertia::render('Checkout', [
            'plan' => $plan->only('id', 'name', 'price', 'currency'),
            'taxes' => Tax::whereEnabled(true)->get(['name', 'country', 'rate', 'inclusive']),
            'countries' => CountryFilterResource::collection(CountryAlpha2::cases()),
            'paymentMethods' => $plan->price > 0 ? $paymentMethods : [BankTransfer::label()]
        ]);
    }

    public function subscribe(): Response
    {
        Gate::authorize('subscribe', Subscription::class);

        $subscription = Auth::user()->currentSubscription;

        $plans = Plan::whereEnabled(true)
                     ->when($subscription, fn($query) => $query->orWhere('id', $subscription->plan_id))
                     ->orderBy('position')
                     ->get()
                     ->each(fn($plan) => $plan->criteria = $plan->criteria(Auth::user()));

        return Inertia::render('Subscribe', [
            'plans' => $plans,
            'subscription' => $subscription
        ]);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(CancelSubscriptionData $data, Subscription $subscription): RedirectResponse
    {
        Gate::authorize('cancel', $subscription);

        try {
            $subscription->cancel($data->immediate);
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back()->with('success', __('messages.subscription.cancelled'));
    }

    public function assign(AssignSubscriptionData $data): RedirectResponse
    {
        Gate::authorize('assign', [Subscription::class, $data->user, $data->plan]);

        Subscription::create([
            'renewal_at' => $data->renewalAt,
            'ends_at' => $data->endsAt,
            'plan_id' => $data->plan->id,
            'user_id' => $data->user->id,
            'status' => SubscriptionStatus::Active,
            'subscription_id' => Str::uuid()
        ]);

        return Redirect::back()->with('success', __('messages.subscription.assigned'));
    }
}
