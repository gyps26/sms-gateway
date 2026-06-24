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

use App\Models\SendingServer;

class SendingServerObserver
{
    /**
     * Handle the SendingServer "created" event.
     */
    public function created(SendingServer $sendingServer): void
    {
        $sendingServer->quota()->create([
            'enabled' => false,
            'value' => 100,
            'frequency' => 1,
            'frequency_unit' => 'Day',
            'available' => 100,
            'reset_at' => now()->addDay(),
        ]);
    }

    /**
     * Handle the SendingServer "updated" event.
     */
    public function updated(SendingServer $sendingServer): void
    {
        //
    }

    /**
     * Handle the SendingServer "deleted" event.
     */
    public function deleted(SendingServer $sendingServer): void
    {
        //
    }

    /**
     * Handle the SendingServer "restored" event.
     */
    public function restored(SendingServer $sendingServer): void
    {
        //
    }

    /**
     * Handle the SendingServer "force deleted" event.
     */
    public function forceDeleted(SendingServer $sendingServer): void
    {
        //
    }
}
