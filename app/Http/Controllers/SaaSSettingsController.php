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

use App\Data\Settings\BillingInfoSettingsData;
use App\Data\Settings\CreditsSettingsData;
use App\Http\Requests\UpdateTrialSettingsRequest;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class SaaSSettingsController extends Controller
{
    public function edit()
    {
        Gate::authorize('updateAny', Setting::class);

        return Inertia::render('Settings/SaaS/Show', [
            'plans' => Plan::all(['id', 'name', 'price', 'currency'])->append('label'),
            'settings' => config('saas')
        ]);
    }

    public function billingInfo(BillingInfoSettingsData $data): RedirectResponse
    {
        Setting::store('saas.billing_info', $data->billingInfo);

        return Redirect::back();
    }

    public function credits(CreditsSettingsData $data): RedirectResponse
    {
        Setting::store('saas.credits.received.amount', $data->received);
        Setting::store('saas.credits.sms.amount', $data->sms);
        Setting::store('saas.credits.sms.per_part', $data->perPart);
        Setting::store('saas.credits.mms.amount', $data->mms);
        Setting::store('saas.credits.ussd_pull.amount', $data->ussdPull);
        Setting::store('saas.credits.call.amount', $data->call);
        Setting::store('saas.credits.webhook_call.amount', $data->webhookCall);
        Setting::store('saas.credits.message_to_email.amount', $data->messageToEmail);
        Setting::store('saas.credits.email_to_message.amount', $data->emailToMessage);

        return Redirect::back();
    }

    public function trial(UpdateTrialSettingsRequest $request): RedirectResponse
    {
        Setting::store('saas.trial.plan_id', $request->validated('plan'));
        Setting::store('saas.trial.duration', $request->validated('duration'));
        Setting::store('saas.trial.footer', $request->validated('footer'));

        return Redirect::back();
    }
}
