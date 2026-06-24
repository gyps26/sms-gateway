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

use App\Models\PaymentGateway;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PaymentGatewaySettingsController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Settings/PaymentGateway/Show', [
            'paymentGateways' => PaymentGateway::all()
        ]);
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, PaymentGateway $paymentGateway): RedirectResponse
    {
        Gate::authorize('updateAny', Setting::class);

        $rules = [
            'enabled' => ['required', 'boolean']
        ];

        foreach ($paymentGateway->fields as $key => $setting) {
            $rules["config.$key"] = ['required_if_accepted:enabled', $setting['type'] === 'boolean' ? 'boolean' : 'string'];
        }

        $data = $request->validate($rules);

        Setting::store("payment-gateways.$paymentGateway->name.enabled", $data['enabled']);
        foreach ($data['config'] as $key => $value) {
            Setting::store("payment-gateways.$paymentGateway->name.$key", $value);
        }

        return Redirect::back();
    }
}
