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

class ProcessContactListsCampaign extends ProcessCampaign
{
    protected Campaign $campaign;

    private Monitor $monitor;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->monitor = Monitor::for(Campaign::class, $this->campaign->id);
        $this->monitor->total = $campaign->contactLists->sum(function ($contactList) {
            return $contactList->contacts()->whereSubscribed(true)->count();
        });
        $this->monitor->processed = $this->monitor->failures = 0;
    }

    public function process(): void
    {
        $senderCount = $this->campaign->senders->count();
        $force = $this->campaign->options->get('force', false);

        $created = 0;
        foreach ($this->campaign->contactLists as $contactList) {
            $contacts = $contactList->contacts()
                                    ->with('contactFields')
                                    ->whereSubscribed(true)
                                    ->lazy(100);

            $blacklist = when(
                $force,
                new Collection(),
                function () use ($contacts) {
                    return $this->campaign->user->blacklist()
                                                ->whereIn('mobile_number', $contacts->pluck('mobile_number'))
                                                ->pluck('mobile_number');
                }
            );

            $messages = [];
            foreach ($contacts as $contact) {
                if ($this->monitor->cancelled) {
                    $this->job->delete();
                    return;
                }

                if ($blacklist->doesntContain($contact->mobile_number)) {
                    $contact->setRelation('contactList', $contactList);
                    $additional = $contact->extras + ['contact_list' => $contact->contact_list_id, 'mobile_number' => $contact->mobile_number];
                    $senderIndex = $senderCount > 0 ? $created % $senderCount : 0;
                    $messages[] = $this->campaign->makeMessage($contact->mobile_number, $this->campaign->senders->get($senderIndex), $additional);
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
