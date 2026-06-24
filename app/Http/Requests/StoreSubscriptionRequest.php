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

namespace App\Http\Requests;

use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Subscription;
use App\PaymentGateways\BankTransfer;
use App\Rules\Coupon;
use Illuminate\Auth\Access\Response;
use Illuminate\Container\Attributes\RouteParameter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreSubscriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(#[RouteParameter('plan')] Plan $plan): bool|Response
    {
        return when(
            Gate::allows('view', $plan),
            Gate::inspect('create', [Subscription::class, $plan]),
            false
        );
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(#[RouteParameter('plan')] Plan $plan): array
    {
        return [
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'company' => ['nullable', 'string'],
            'city' => ['required', 'string', 'max:255'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', $plan->price > 0 ? Rule::in(PaymentGateway::all()->pluck('label')) : Rule::in([BankTransfer::label()])],
            'coupon' => ['nullable', new Coupon()],
        ];
    }
}
