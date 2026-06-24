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

use App\Models\Plan;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, #[CurrentUser] ?User $user): Response|RedirectResponse
    {
        if (config('app.homepage')) {
            $plans = Plan::whereEnabled(true)
                         ->orderBy('position')
                         ->get(['id', 'name', 'price', 'currency', 'features', 'position', 'interval', 'interval_unit'])
                         ->each(fn($plan) => $plan->criteria = Auth::check() ? $plan->criteria(Auth::user()) : null);

            return Inertia::render('Home', [
                'announcement' => config('app.announcements.guest'),
                'plans' => $plans,
                'subscription' => $user?->currentSubscription,
                'supportUrl' => config('app.support_url')
            ]);
        } else {
            return Redirect::route('login');
        }
    }
}
