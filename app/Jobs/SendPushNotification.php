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

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\Device;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Throwable;

#[DeleteWhenMissingModels]
class SendPushNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Device $device;

    private Campaign $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, Device $device)
    {
        $this->campaign = $campaign;
        $this->device = $device;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $token = $this->device->firebase_token;

        if (is_null($token) || is_null(config('firebase.projects.app.credentials'))) return;

        /** @var \Kreait\Firebase\Messaging $messaging */
        $messaging = app('firebase.messaging');

        $config = AndroidConfig::new()->withHighMessagePriority();

        $message = CloudMessage::new()
                               ->toToken($token)
                               ->withAndroidConfig($config)
                               ->withData(['campaign_id' => $this->campaign->id]);

        try {
            $messaging->send($message);
        } catch (Throwable) {
            // ignored
        }
    }
}
