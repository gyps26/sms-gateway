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

namespace App\Http\Middleware;

use App\Helpers\Common;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Middleware;
use Laravel\Fortify\Features;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app' => fn() => [
                'apk' => transform(
                    config('app.apk_path'),
                    fn($path) => Storage::disk('public')->url($path),
                    'https://rbsoft.org/downloads/sms-gateway/sms-gateway-beta.apk'
                ),
                'name' => config('app.name'),
                'logo' => transform(
                    config('app.logo_path'),
                    fn($path) => Storage::disk('public')->url($path),
                    asset('images/logo.png')
                )
            ],
            'locale' => fn() => [
                'current' => App::getLocale(),
                'messages' => Common::getTranslations('ui') + Common::getTranslations('ui', App::getFallbackLocale()),
                'available' => Common::getAvailableLocales()
            ],
            'flash' => Inertia::always([
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error')
            ]),
            'plansCount' => function () {
                return Common::isInstalled()
                    ? Cache::rememberForever('plans_count', fn() => Plan::whereEnabled(true)->count())
                    : 0;
            },
            'canRegister' => fn() => Features::enabled(Features::registration()) && config('app.registration'),
            'isImpersonating' => fn() => app('impersonate')->isImpersonating()
        ]);
    }
}
