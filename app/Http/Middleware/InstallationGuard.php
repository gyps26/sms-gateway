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

use App\Installer;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InstallationGuard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installer = new Installer();

        $checks = [
            'install.requirements' => function () use ($installer) {
                if (Arr::any($installer->dependencies, false)) {
                    Session::flash('error', __('messages.installer.errors.depedencies'));

                    return false;
                }

                return true;
            },
            'install.permissions' => function () use ($installer) {
                if (Arr::any($installer->permissions, false)) {
                    Session::flash('error', __('messages.installer.errors.permissions'));

                    return false;
                }

                return true;
            },
            'install.database' => function () {
                try {
                    DB::connection()->getPdo();

                    return true;
                } catch (Throwable $t) {
                    Session::flash('error',  __('messages.installer.errors.database', ['message' => $t->getMessage()]));

                    return false;
                }
            },
            'install.admin' => function () {
                if (Schema::hasTable('users') && User::whereIsAdmin(true)->exists()) {
                    return true;
                }

                Session::flash('error', __('messages.installer.errors.admin'));

                return false;
            },
        ];

        foreach ($checks as $route => $check) {
            if ($route !== Route::currentRouteName()) {
                if ($check()) {
                    continue;
                }

                return Inertia::location(route($route));
            }

            break;
        }

        return $next($request);
    }
}
