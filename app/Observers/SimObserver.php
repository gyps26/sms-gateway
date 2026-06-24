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

use App\Enums\FrequencyUnit;
use App\Models\Sim;

class SimObserver
{
    /**
     * Handle the Sim "created" event.
     */
    public function created(Sim $sim): void
    {
        $sim->quota()->create([
            'value' => 100,
            'frequency' => 1,
            'frequency_unit' => FrequencyUnit::Day,
            'available' => 100,
            'reset_at' => now()->addDay()->startOfDay()
        ]);
    }

    /**
     * Handle the Sim "updated" event.
     */
    public function updated(Sim $sim): void
    {
        //
    }

    /**
     * Handle the Sim "deleted" event.
     */
    public function deleted(Sim $sim): void
    {
        //
    }

    /**
     * Handle the Sim "restored" event.
     */
    public function restored(Sim $sim): void
    {
        //
    }

    /**
     * Handle the Sim "force deleted" event.
     */
    public function forceDeleted(Sim $sim): void
    {
        //
    }
}
