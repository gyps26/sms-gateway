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

use App\Data\Settings\AnnouncementSettingsData;
use App\Data\Settings\DashboardSettingsData;
use App\Data\Settings\GeneralSettingsData;
use App\Data\Settings\RegistrationSettingsData;
use App\Helpers\Common;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class GeneralSettingsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(): Response
    {
        Gate::authorize('updateAny', Setting::class);

        $appSettings = Arr::only(
            config('app'),
            ['name', 'support_url', 'homepage', 'registration', 'announcements', 'dashboard']
        );

        $appSettings['support_url'] = Str::replace(['mailto:', 'tel:'], ['mailto://', 'tel://'], $appSettings['support_url']);

        $servicesSettings = [
            'recaptcha' => config('services.recaptcha'),
        ];

        return Inertia::render('Settings/General/Show', [
            'logoUrl' => transform(
                Setting::retrieve('app.logo_path'),
                fn($path) => Storage::disk('public')->url($path)
            ),
            'settings' => $appSettings + $servicesSettings
        ]);
    }

    public function general(GeneralSettingsData $data): RedirectResponse
    {
        Setting::store('app.name', $data->name);
        Setting::store('app.support_url', Str::replace(['mailto://', 'tel://'], ['mailto:', 'tel:'], $data->supportUrl));
        Setting::store('app.homepage', $data->homepage);

        if ($data->logo) {
            if (Setting::retrieve('app.logo_path')) {
                Storage::disk('public')->delete(Setting::retrieve('app.logo_path'));
            }

            $path = $data->logo->store('logo', 'public');
            if ($path) {
                $fullPath = Storage::disk('public')->path($path);

                Common::generateFavicon($fullPath);

                Setting::store('app.logo_path', $path);
            }
        }

        if ($data->app) {
            if (Setting::retrieve('app.apk_path')) {
                Storage::disk('public')->delete(Setting::retrieve('app.apk_path'));
            }

            $file = $data->app->storeAs('apk', 'sms-gateway.apk', 'public');
            if ($file) {
                Setting::store('app.apk_path', $file);
            }
        }

        return Redirect::back();
    }

    public function registration(RegistrationSettingsData $data): RedirectResponse
    {
        Setting::store('app.registration', $data->enabled);

        if ($data->recaptcha) {
            Setting::store('services.recaptcha.site_key', $data->recaptcha->siteKey);
            Setting::store('services.recaptcha.secret_key', $data->recaptcha->secretKey);
        }

        return Redirect::back();
    }

    public function announcement(AnnouncementSettingsData $data): RedirectResponse
    {
        Setting::store('app.announcements.guest', $data->guest);
        Setting::store('app.announcements.member', $data->member);

        return Redirect::back();
    }

    public function destroyLogo(): RedirectResponse
    {
        $logoPath = Setting::retrieve('app.logo_path');

        Storage::disk('public')->delete($logoPath);

        $defaultLogoPath = public_path('images') . DIRECTORY_SEPARATOR . 'logo.png';

        Common::generateFavicon($defaultLogoPath);

        Setting::store('app.logo_path', null);

        return Redirect::back();
    }

    public function dashboard(DashboardSettingsData $data): RedirectResponse
    {
        Setting::store('app.dashboard.stats.campaigns', $data->campaigns);
        Setting::store('app.dashboard.stats.calls', $data->calls);
        Setting::store('app.dashboard.stats.messages', $data->messages);
        Setting::store('app.dashboard.stats.ussd_pulls', $data->ussdPulls);
        Setting::store('app.dashboard.realtime', $data->realtime);

        return Redirect::back();
    }
}
