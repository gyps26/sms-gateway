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

namespace App\Observers;

use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaignable;

class CampaignableObserver
{
    /**
     * Handle the Campaignable "created" event.
     */
    public function created(Campaignable $campaignable): void
    {
        //
    }

    /**
     * Handle the Campaignable "updated" event.
     */
    public function updated(Campaignable $campaignable): void
    {
        $completed = [CampaignableStatus::Succeeded, CampaignableStatus::Failed, CampaignableStatus::Cancelled];

        if (in_array($campaignable->status, $completed)) {
            $statuses= Campaignable::whereCampaignId($campaignable->campaign_id)->pluck('status');
            if ($statuses->every(fn($status) => in_array($status, $completed))) {
                $campaignable->campaign->update(['status' => CampaignStatus::Completed]);
            }
        }
    }

    /**
     * Handle the Campaignable "deleted" event.
     */
    public function deleted(Campaignable $campaignable): void
    {
        //
    }

    /**
     * Handle the Campaignable "restored" event.
     */
    public function restored(Campaignable $campaignable): void
    {
        //
    }

    /**
     * Handle the Campaignable "force deleted" event.
     */
    public function forceDeleted(Campaignable $campaignable): void
    {
        //
    }
}
