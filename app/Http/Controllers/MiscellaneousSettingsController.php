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

use App\Data\Settings\FirebaseSettingsData;
use App\Data\Settings\QrCodeSettingsData;
use App\Data\Settings\WebhookServerSettingsData;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class MiscellaneousSettingsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(): Response
    {
        Gate::authorize('updateAny', Setting::class);

        $webhookServer = Arr::only(
            config('webhook-server'),
            ['tries', 'timeout_in_seconds', 'verify_ssl']
        );

        $settings = [
            'webhook_server' => $webhookServer,
            'qr_code' => config('auth.qr_code'),
            'firebase_project_id' => config('firebase.projects.app.credentials.project_id'),
        ];

        return Inertia::render('Settings/Misc/Show', [
            'settings' => $settings,
        ]);
    }

    public function firebase(FirebaseSettingsData $data)
    {
        if ($data->serviceAccountJson) {
            Setting::store('firebase.projects.app.credentials', json_decode($data->serviceAccountJson->getContent()));
        }

        return Redirect::back();
    }

    public function qrCode(QrCodeSettingsData $data)
    {
        Setting::store('auth.qr_code.lifespan', $data->lifespan);

        return Redirect::back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function webhook(WebhookServerSettingsData $data): RedirectResponse
    {
        Setting::store('webhook-server.tries', $data->tries);
        Setting::store('webhook-server.timeout_in_seconds', $data->timeout);
        Setting::store('webhook-server.verify_ssl', $data->verifySsl);

        return Redirect::back();
    }
}
