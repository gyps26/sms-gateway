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
use App\Models\UssdPull;
use App\Monitor;

class ProcessUssdCampaign extends ProcessCampaign
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
        $this->monitor->total = count($campaign->payload->get('ussd_codes', []));
        $this->monitor->processed = $this->monitor->failures = 0;
    }

    public function process(): void
    {
        $ussdCodes = collect($this->campaign->payload->get('ussd_codes', []))->chunk(100);
        $simsCount = $this->campaign->sims->count();

        $ussdPulls = [];
        foreach ($ussdCodes as $chunk) {
            foreach ($chunk as $ussdCode) {
                $simIndex = $simsCount > 0 ? $this->monitor->processed % $simsCount : 0;
                $sim = $this->campaign->sims->get($simIndex);
                $ussdPulls[] = $this->campaign->makeUssdPull($ussdCode, $sim);
                $this->monitor->processed++;
            }

            UssdPull::insert($ussdPulls);
        }
    }
}
