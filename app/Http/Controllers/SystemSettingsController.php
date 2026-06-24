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

use App\Data\Settings\CronSettingsData;
use App\Data\Settings\LicenseSettingsData;
use App\Data\Settings\UpdateSystemData;
use App\Helpers\Environment;
use App\Models\Setting;
use App\Zip;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\Process\PhpExecutableFinder;

class SystemSettingsController extends Controller
{
    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(): Response
    {
        Gate::authorize('updateAny', Setting::class);

        $php = when((new PhpExecutableFinder())->find(), fn($path) => $path, 'php');

        $artisan = base_path('artisan');

        return Inertia::render('Settings/System/Show', [
            'version' => config('app.version'),
            'licenseCode' => config('app.license_code'),
            'queueUp' => config('queue.default') === 'database',
            'maxUploadSize' => Environment::getUploadMaxSize(),
            'cron' => [
                'command' => "$php -q $artisan schedule:run >> /dev/null 2>&1",
                'executed_at' => Cache::get('cron_executed_at'),
            ],
        ]);
    }

    public function update(UpdateSystemData $data): RedirectResponse
    {
        try {
            Zip::extract($data->update, function ($entryName) {
                $except = [
                    '.env',
                    'public/android-chrome-192x192.png',
                    'public/android-chrome-512x512.png',
                    'public/apple-touch-icon.png',
                    'public/favicon-16x16.png',
                    'public/favicon-32x32.png',
                    'public/site.webmanifest'
                ];

                if (in_array($entryName, $except)) {
                    return null;
                }

                $parts = explode('/', $entryName);
                return base_path(implode(DIRECTORY_SEPARATOR, $parts));
            });

            Artisan::call('migrate:status');
            if (Str::contains(Artisan::output(), 'Pending')) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }

        return Redirect::back();
    }

    public function license(LicenseSettingsData $data): RedirectResponse
    {
        Setting::store('app.license_code', $data->code);

        return Redirect::back();
    }

    public function cron(CronSettingsData $data)
    {
        Setting::store('queue.default', $data->queueUp ? 'database' : 'sync');

        return Redirect::back();
    }
}
