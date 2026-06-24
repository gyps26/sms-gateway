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

use App\Enums\WebhookCallStatus;
use App\Models\WebhookCall;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\WebhookServer\Events\WebhookCallFailedEvent;
use Spatie\WebhookServer\Events\WebhookCallSucceededEvent;

class ProcessWebhookCallResult
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
    public function handle(WebhookCallSucceededEvent|WebhookCallFailedEvent $event): void
    {
        $webhookCall = WebhookCall::findOrFail($event->meta['id']);

        if ($event->response) {
            $webhookCall->update([
                'response' => $event->response->getBody()->getContents(),
                'status_code' => $event->response->getStatusCode(),
                'attempts' => $event->attempt,
                'status' => $event->response->getStatusCode() >= 200 && $event->response->getStatusCode() < 300
                    ? WebhookCallStatus::Success
                    : ($event->attempt >= config('webhook-server.tries') ? WebhookCallStatus::PermanentlyFailed : WebhookCallStatus::Failed),
                'last_retry_at' => now(),
            ]);
        } else {
            $webhookCall->update([
                'response' => $event->errorMessage,
                'status' => ($event->attempt >= config('webhook-server.tries') ? WebhookCallStatus::PermanentlyFailed : WebhookCallStatus::Failed),
                'attempts' => $event->attempt,
                'last_retry_at' => now(),
            ]);
        }
    }
}
