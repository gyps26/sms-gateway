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

use App\Data\SendCampaignData;
use App\Enums\WebhookEvent;
use App\Events\MessageReceived;
use Illuminate\Contracts\Queue\ShouldQueue;

class RespondToReceivedMessage implements ShouldQueue
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
     *
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Throwable
     */
    public function handle(MessageReceived $event): void
    {
        foreach ($event->user->autoResponses()->lazy(chunkSize: 100) as $autoResponse) {
            if ($autoResponse->matches($event->received->content) === false) {
                continue;
            }

            $data = SendCampaignData::from([
                'type' => $autoResponse->type,
                'timezone' => 'UTC',
                'days_of_week' => [1, 2, 3, 4, 5, 6, 7],
                'active_hours' => ['start' => '00:00', 'end' => '23:59'],
                'mobile_numbers' => [$event->received->from],
                'message' => $autoResponse->response,
                'delay' => 0,
                'prioritize' => true,
                'sims' => [$event->received->messageable_id],
                'attachments' => $autoResponse->getMedia('attachments')->all()
            ]);

            $event->user->createMessageCampaign($data);
        }

        $event->user->callWebhooks(
            WebhookEvent::MessageReceived,
            $event->received,
            ['status', 'retries', 'response', 'campaign_id', 'user_id']
        );
    }
}
