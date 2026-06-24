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

use App\Data\Settings\AutoRetrySettingsData;
use App\Data\Settings\PhoneIdSettingsData;
use App\Data\Settings\PromptsSettingsData;
use App\Data\Settings\SimMessagingSettingsData;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MessagingSettingsController extends Controller
{
    public function edit()
    {
        Gate::authorize('updateAny', Setting::class);

        return Inertia::render('Settings/Messaging/Show', [
            'settings' => Arr::only(config('messaging'), ['auto_retry', 'prompts', 'phone_id', 'sim'])
        ]);
    }

    public function sim(SimMessagingSettingsData $data): RedirectResponse
    {
        Setting::store('messaging.sim.wait_for_confirmation', $data->waitForConfirmation);

        return Redirect::back();
    }

    public function autoRetry(AutoRetrySettingsData $data): RedirectResponse
    {
        Setting::store('messaging.auto_retry.enabled', $data->enabled);
        Setting::store('messaging.auto_retry.max_attempts', $data->maxAttempts);
        Setting::store('messaging.auto_retry.change_after', $data->changeAfter);

        return Redirect::back();
    }

    public function prompts(PromptsSettingsData $data): RedirectResponse
    {
        Setting::store('messaging.prompts.keywords.blacklist', $data->blacklist);
        Setting::store('messaging.prompts.keywords.whitelist', $data->whitelist);
        Setting::store('messaging.prompts.keywords.subscribe', $data->subscribe);
        Setting::store('messaging.prompts.keywords.unsubscribe', $data->unsubscribe);
        Setting::store('messaging.prompts.notify', $data->notify);

        return Redirect::back();
    }

    public function phoneId(PhoneIdSettingsData $data)
    {
        Setting::store('messaging.phone_id.enabled', $data->enabled);
        Setting::store('messaging.phone_id.contact_field_tag', $data->contactFieldTag);

        return Redirect::back();
    }
}
