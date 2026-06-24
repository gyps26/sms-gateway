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
use App\Models\Message;
use App\Monitor;
use Illuminate\Support\Collection;

class ProcessManualCampaign extends ProcessCampaign
{
    protected Campaign $campaign;

    private Monitor $monitor;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->monitor = Monitor::for(Campaign::class, $this->campaign->id);
        $this->monitor->total = count($this->campaign->payload->get('mobile_numbers', []));
        $this->monitor->processed = $this->monitor->failures = 0;
    }

    public function process(): void
    {
        $mobileNumbers = collect($this->campaign->payload->get('mobile_numbers', []))->chunk(100);
        $senderCount = $this->campaign->senders->count();
        $force = $this->campaign->options->get('force', false);

        $created = 0;
        foreach ($mobileNumbers as $chunk) {
            $blacklist = when(
                $force,
                new Collection(),
                function () use ($chunk) {
                    return $this->campaign->user->blacklist()
                                                ->whereIn('mobile_number', $chunk)
                                                ->pluck('mobile_number');
                }
            );

            $messages = [];
            foreach ($chunk as $mobileNumber) {
                if ($this->monitor->cancelled) {
                    $this->job->delete();
                    break;
                }

                if ($blacklist->doesntContain($mobileNumber)) {
                    $senderIndex = $senderCount > 0 ? $created % $senderCount : 0;
                    $sender = $this->campaign->senders->get($senderIndex);
                    $messages[] = $this->campaign->makeMessage($mobileNumber, $sender);
                    $created++;
                }

                $this->monitor->processed++;
            }

            if ($messages) {
                Message::insert($messages);
            }
        }
    }
}
