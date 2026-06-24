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

use App\Data\Settings\FromAddressSettingsData;
use App\Data\Settings\ImapSettingsData;
use App\Data\TestMailerData;
use App\Http\Requests\UpdateMailerRequest;
use App\Mail\Test;
use App\Models\Setting;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Webklex\IMAP\Facades\Client;

class MailSettingsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(): Response
    {
        Gate::authorize('updateAny', Setting::class);

        return Inertia::render('Settings/Mail/Show', [
            'settings' => [
                'mailer' => [
                    'default' => config('mail.default'),
                    'from' => config('mail.from'),
                ],
                'mailers' => array_merge(
                    Arr::only(config('mail.mailers'), ['smtp', 'sendmail']),
                    Arr::only(config('services'), ['mailgun', 'postmark', 'ses', 'resend'])
                ),
                'imap' => config('imap.accounts.default', []),
                'features' => Arr::only(config('messaging'), ['email_to_message', 'message_to_email']),
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function mailer(UpdateMailerRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Setting::store('mail.default', data_get($data, 'mailer'));

        if (data_get($data, 'sendmail')) {
            Setting::store('mail.mailers.sendmail.path', data_get($data, 'sendmail.path'));
        }

        if (data_get($data, 'smtp')) {
            Setting::store('mail.mailers.smtp.host', data_get($data, 'smtp.host'));
            Setting::store('mail.mailers.smtp.port', data_get($data, 'smtp.port'));
            Setting::store('mail.mailers.smtp.encryption', data_get($data, 'smtp.encryption'));
            Setting::store('mail.mailers.smtp.username', data_get($data, 'smtp.username'));
            Setting::store('mail.mailers.smtp.password', data_get($data, 'smtp.password'));
        }

        if (data_get($data, 'mailgun')) {
            Setting::store('services.mailgun.domain', data_get($data, 'mailgun.domain'));
            Setting::store('services.mailgun.secret', data_get($data, 'mailgun.secret'));
            Setting::store('services.mailgun.endpoint', data_get($data, 'mailgun.endpoint'));
        }

        if (data_get($data, 'ses')) {
            Setting::store('services.ses.key', data_get($data, 'ses.key'));
            Setting::store('services.ses.secret', data_get($data, 'ses.secret'));
            Setting::store('services.ses.region', data_get($data, 'ses.region'));
        }

        if (data_get($data, 'postmark')) {
            Setting::store('services.postmark.token', data_get($data, 'postmark.token'));
        }

        if (data_get($data, 'resend')) {
            Setting::store('services.resend.key', data_get($data, 'resend.key'));
        }

        Setting::store('messaging.message_to_email', data_get($data, 'message_to_email'));

        return Redirect::back();
    }

    public function imap(ImapSettingsData $data): RedirectResponse
    {
        Setting::store('imap.accounts.default.email', $data->email);
        Setting::store('imap.accounts.default.host', $data->host);
        Setting::store('imap.accounts.default.port', $data->port);
        Setting::store('imap.accounts.default.protocol', $data->protocol);
        Setting::store('imap.accounts.default.encryption', $data->encryption === 'none' ? false : $data->encryption);
        Setting::store('imap.accounts.default.validate_cert', $data->validateCert);
        Setting::store('imap.accounts.default.username', $data->username);
        Setting::store('imap.accounts.default.password', $data->password);
        Setting::store('messaging.email_to_message', $data->emailToMessage);

        return Redirect::back();
    }

    public function fromAddress(FromAddressSettingsData $data): RedirectResponse
    {
        Setting::store('mail.from.name', $data->name);
        Setting::store('mail.from.address', $data->email);

        return Redirect::back();
    }

    public function testMailer(TestMailerData $data): RedirectResponse
    {
        try {
            Mail::to($data->email)->sendNow(new Test());

            return Redirect::back()->with('success', __('messages.mailer.test.success'));
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    public function testImap()
    {
        try {
            $client = Client::account('default');
            $client->connect();
            $client->getFolder('INBOX');
            $client->disconnect();

            return Redirect::back()->with('success', __('messages.imap.test.success'));
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }
}
