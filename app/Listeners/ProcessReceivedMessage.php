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

namespace App\Listeners;

use App\Events\MessageReceived;
use App\Helpers\Validators;
use App\Jobs\ProcessManualCampaign;
use App\Models\Campaign;
use App\Models\ContactList;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Str;

class ProcessReceivedMessage implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageReceived $event): void
    {
        $message = Str::lower($event->received->content);
        if (! Validators::isMobileNumber($event->received->from)) {
            return;
        }

        $prompts = Setting::retrieve('messaging.prompts', $event->user->id, []) + config('messaging.prompts');

        $blacklist = transform(data_get($prompts, 'keywords.blacklist'), fn($value) => $message === Str::lower($value));
        $whitelist = transform(data_get($prompts, 'keywords.whitelist'), fn($value) => $message === Str::lower($value));
        $subscribe = transform(data_get($prompts, 'keywords.subscribe'), fn($value) => mb_stripos($message, $value) === 0);
        $unsubscribe = transform(data_get($prompts, 'keywords.unsubscribe'), fn($value) => stripos($message, $value) === 0);
        $notify = data_get($prompts, 'notify', true);

        $content = null;
        if ($blacklist) {
            $event->user->blacklist()->firstOrCreate(['mobile_number' => $event->received->from]);
            $content = __('messages.prompts.blacklist');
            if ($whitelistKeyword = data_get($prompts, 'keywords.whitelist')) {
                $content .= ' ' . __('messages.prompts.whitelist_or_subscribe', ['prompt' => $whitelistKeyword]);
            } else {
                $content .= ' ' . __('messages.prompts.general');
            }
        } else if ($whitelist) {
            $event->user->blacklist()->whereMobileNumber($event->received->from)->first()->delete();
            $content = __('messages.prompts.whitelist');
        } else if ($subscribe || $unsubscribe) {
            $parts = explode(' ', $message);
            if (count($parts) === 2 && ctype_digit($parts[1])) {
                /** @var ContactList $contactList */
                $contactList = ContactList::find($parts[1]);
                if (is_null($contactList)) {
                    return;
                }

                $contact = $contactList->contacts()->whereMobileNumber($event->received->from)->first();
                if (is_null($contact)) {
                    return;
                }

                if ($subscribe) {
                    if ($contact->subscribed) {
                        return;
                    }

                    $contact->update(['subscribed' => true]);

                    $content = __('messages.prompts.subscribe');
                } else {
                    if ($contact->subscribed) {
                        $contact->update(['subscribed' => false]);

                        $content = __('messages.prompts.unsubscribe');
                        if ($subscribeKeyword = data_get($prompts, 'keywords.subscribe')) {
                            $content .= ' ' . __('messages.prompts.whitelist_or_subscribe', ['prompt' => $subscribeKeyword]);
                        } else {
                            $content .= ' ' . __('messages.prompts.general');
                        }
                    }
                }
            }
        }

        if ($notify && $content) {
            $campaign = Campaign::create([
                'type' => 'SMS',
                'timezone' => 'UTC',
                'days_of_week' => [1, 2, 3, 4, 5, 6, 7],
                'active_hours' => '00:00-23:59',
                'user_id' => $event->user->id,
                'payload' => [
                    'mobile_numbers' => [$event->received->from],
                    'message' => $content
                ],
                'options' => [
                    'delay' => 0,
                    'force' => true,
                    'prioritize' => true,
                ]
            ]);

            $campaign->attachSims([$event->received->messageable_id]);

            ProcessManualCampaign::dispatch($campaign);
        }
    }
}
