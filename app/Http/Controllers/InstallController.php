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

use App\Actions\Fortify\PasswordValidationRules;
use App\Installer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;
use Symfony\Component\Process\PhpExecutableFinder;
use Throwable;

class InstallController extends Controller
{
    use PasswordValidationRules;

    public function show(): Response
    {
        return Inertia::render('Install/Show');
    }

    public function requirements(Installer $installer): Response
    {
        return Inertia::render('Install/Requirements', [
            'dependencies' => $installer->dependencies,
        ]);
    }

    public function permissions(Installer $installer): Response
    {
        return Inertia::render('Install/Permissions', [
            'permissions' => $installer->permissions,
        ]);
    }

    public function database(Installer $installer): Response
    {
        return Inertia::render('Install/Database', [
            'params' => $installer->getDbConfig()
        ]);
    }

    public function admin(): Response
    {
        return Inertia::render('Install/Admin');
    }

    public function completed(Installer $installer): Response
    {
        if ($installer->markInstalled() === false) {
            Session::flash('error', __('messages.installer.errors.completed'));
        }

        $php = (new PhpExecutableFinder())->find();
        if ($php === false) {
            $php = 'php';
        }

        $artisan = base_path('artisan');

        return Inertia::render('Install/Completed', [
            'command' => "$php -q $artisan schedule:run >> /dev/null 2>&1",
        ]);
    }

    public function storeConfig(Request $request, Installer $installer): BaseResponse
    {
        $data = $request->validate([
            'host' => ['required', 'string'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'name' => ['required', 'string'],
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
        ]);

        try {
            $installer->createConfig($data);

            return Redirect::to(route('install.admin'));
        } catch (Throwable $t) {
            return Redirect::back()->with('error', $t->getMessage());
        }
    }

    public function storeAdmin(Request $request, Installer $installer): BaseResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
            'license_code' => ['required', 'string', 'max:255'],
        ]);

        try {
            $installer->createAdmin($data);

            return Redirect::to(route('install.completed'));
        } catch (Throwable $t) {
            return Redirect::back()->with('error', $t->getMessage());
        }
    }
}
