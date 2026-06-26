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

use App\Console\Commands\DeleteUsers;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Enums\SubscriptionStatus;
use App\FetchEmails;
use App\Models\AutoResponse;
use App\Models\Campaign;
use App\Models\Campaignable;
use App\Models\Device;
use App\Models\Message;
use App\Models\Quota;
use App\Models\SendingServer;
use App\Models\Sim;
use App\Models\Subscription;
use App\Models\WebhookCall;
use App\Models\WebhookRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schedule;
use Laravel\Telescope\TelescopeServiceProvider;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

Schedule::call(fn() => Quota::whereEnabled(true)->lazy(100)->each->reset())
        ->name('quota')
        ->everyMinute()
        ->withoutOverlapping();

Schedule::when(config('messaging.email_to_message'))
        ->call(new FetchEmails())
        ->everyMinute()
        ->name('email-to-message')
        ->withoutOverlapping();

Schedule::call(
        function () {
            Campaignable::whereStatus(CampaignableStatus::Stalled)
                        ->where('resume_at', '<=', now())
                        ->lazyById(100)
                        ->each
                        ->send();

            Campaign::whereRecurring(true)
                    ->where('repeat_at', '<=', now())
                    ->where(fn($query) => $query->whereRaw('ends_at IS NULL')->orWhere('ends_at', '>=', now()))
                    ->lazyById(100)
                    ->each
                    ->repeat();

            Campaign::whereStatus(CampaignStatus::Scheduled)
                    ->where('scheduled_at', '<=', now())
                    ->lazyById(100)
                    ->each
                    ->send();
        }
    )
    ->name('campaign')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::call(function () {
        Subscription::whereIn('status', [SubscriptionStatus::Active, SubscriptionStatus::Trial, SubscriptionStatus::Cancelled])
                    ->where('ends_at', '<=', now())
                    ->lazyById(100)
                    ->each
                    ->update(['status' => SubscriptionStatus::Expired]);

        Subscription::where('status', SubscriptionStatus::Active)
                    ->where(function (Builder $query) {
                        $query->whereNull('payment_method')
                              ->orWhere('payment_method', 'Bank Transfer');
                    })
                    ->where('renewal_at', '<=', now())
                    ->lazyById(100)
                    ->each
                    ->renew();
        })
        ->name('subscription')
        ->everyMinute()
        ->withoutOverlapping();

Schedule::call(function () {
            Campaignable::whereDoesntHaveMorph('campaignable', [Device::class, SendingServer::class])
                        ->delete();

            Quota::whereDoesntHaveMorph('quotable', [SendingServer::class, Sim::class])
                 ->delete();

            Media::whereDoesntHaveMorph('model', [AutoResponse::class, Campaign::class, Message::class])
                 ->cursor()
                 ->each
                 ->delete();
        })
        ->daily();

if (class_exists(DeleteUsers::class)) {
    Schedule::command(DeleteUsers::class)
            ->name('demo')
            ->dailyAt('00:00')
            ->withoutOverlapping();
}

if ($this->app->environment('local') && class_exists(TelescopeServiceProvider::class)) {
    Schedule::command('telescope:prune')->daily();
}

Schedule::command('queue:flush --hours=48')->daily();

Schedule::command('model:prune', ['--model' => [WebhookCall::class, WebhookRequest::class]])->daily();
