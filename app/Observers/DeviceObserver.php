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

use App\Models\Device;

class DeviceObserver
{
    /**
     * Handle the Device "creating" event.
     */
    public function creating(Device $device): void
    {
        //
    }

    /**
     * Handle the Device "created" event.
     */
    public function created(Device $device): void
    {
        $device->users()->attach($device->owner_id);
    }

    /**
     * Handle the Device "updated" event.
     */
    public function updated(Device $device): void
    {
        //
    }

    /**
     * Handle the Device "deleted" event.
     */
    public function deleted(Device $device): void
    {
        //
    }

    /**
     * Handle the Device "restored" event.
     */
    public function restored(Device $device): void
    {
        //
    }

    /**
     * Handle the Device "force deleted" event.
     */
    public function forceDeleted(Device $device): void
    {
        //
    }
}
