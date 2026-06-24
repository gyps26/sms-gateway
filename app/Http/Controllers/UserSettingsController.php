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

use App\Data\Settings\AutoRetryUserSettingsData;
use App\Data\Settings\DashboardUserSettingsData;
use App\Data\Settings\FeaturesUserSettingsData;
use App\Data\Settings\PhoneIdUserSettingsData;
use App\Data\Settings\PromptsUserSettingsData;
use App\Data\Settings\QrCodeSettingsData;
use App\Data\Settings\SimMessagingUserSettingsData;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class UserSettingsController extends Controller
{
    public function edit(): Response
    {
        $globalSettings = [
            ...config('messaging'),
            'dashboard' => config('app.dashboard'),
            'qr_code' => config('auth.qr_code'),
        ];

        $defaultSettings = [
            'auto_retry' => [
                'enabled' => false,
            ],
            'dashboard' => [
                'realtime' => false,
            ],
            'email_to_message' => false,
            'message_to_email' => false,
        ];

        $settings = Setting::retrieve('*', Auth::id(), []);

        $userSettings = [
            ...data_get($settings, 'messaging', []),
            ...data_get($settings, 'app', []),
            ...data_get($settings, 'auth', []),
        ];

        return Inertia::render('Settings/User/Show', [
            'settings' => array_replace_recursive($globalSettings, $defaultSettings, $userSettings),
            'global' => $globalSettings + ['email' => config('imap.accounts.default.email')]
        ]);
    }

    public function sim(SimMessagingUserSettingsData $data)
    {
        Setting::store('messaging.sim.wait_for_confirmation', $data->waitForConfirmation, Auth::id());

        return Redirect::back();
    }

    public function autoRetry(AutoRetryUserSettingsData $data): RedirectResponse
    {
        Setting::store('messaging.auto_retry.enabled', $data->enabled, Auth::id());
        Setting::store('messaging.auto_retry.max_attempts', $data->maxAttempts, Auth::id());
        Setting::store('messaging.auto_retry.change_after', $data->changeAfter, Auth::id());

        return Redirect::back();
    }

    public function features(FeaturesUserSettingsData $data): RedirectResponse
    {
        Setting::store('messaging.email_to_message', $data->emailToMessage, Auth::id());
        Setting::store('messaging.message_to_email', $data->messageToEmail, Auth::id());

        return Redirect::back();
    }

    public function qrCode(QrCodeSettingsData $data)
    {
        Setting::store('auth.qr_code.lifespan', $data->lifespan, Auth::id());

        return Redirect::back();
    }

    public function phoneId(PhoneIdUserSettingsData $data)
    {
        Setting::store('messaging.phone_id.enabled', $data->enabled, Auth::id());
        Setting::store('messaging.phone_id.contact_field_tag', $data->contactFieldTag, Auth::id());

        return Redirect::back();
    }

    public function prompts(PromptsUserSettingsData $data): RedirectResponse
    {
        Setting::store('messaging.prompts.keywords.blacklist', $data->blacklist, Auth::id());
        Setting::store('messaging.prompts.keywords.whitelist', $data->whitelist, Auth::id());
        Setting::store('messaging.prompts.keywords.subscribe', $data->subscribe, Auth::id());
        Setting::store('messaging.prompts.keywords.unsubscribe', $data->unsubscribe, Auth::id());
        Setting::store('messaging.prompts.notify', $data->notify, Auth::id());

        return Redirect::back();
    }

    // Add dashboard update for user
    public function dashboard(DashboardUserSettingsData $data): RedirectResponse
    {
        Setting::store('app.dashboard.stats.campaigns', $data->campaigns, Auth::id());
        Setting::store('app.dashboard.stats.calls', $data->calls, Auth::id());
        Setting::store('app.dashboard.stats.messages', $data->messages, Auth::id());
        Setting::store('app.dashboard.stats.ussd_pulls', $data->ussdPulls, Auth::id());

        return Redirect::back();
    }
}
