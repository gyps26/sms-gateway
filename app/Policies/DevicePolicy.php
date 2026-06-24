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

namespace App\Policies;

use App\Models\Device;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DevicePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Device $device): bool
    {
        return $user->devices()->where('devices.id', $device->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        $count = $user->ownedDevices()->count() + 1;

        return $user->canConsume($count, 'devices');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Device $device): bool
    {
        return $user->id === $device->owner_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Device $device): bool
    {
        return $device->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Device $device): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Device $device): bool
    {
        //
    }

    /**
     * Determine whether the user can use it from the Android application.
     */
    public function app(User $user, Device $device): bool
    {
        return $device->owner_id === $user->id;
    }

    /**
     * Determine whether the user can share a device.
     */
    public function share(User $user, Device $device): bool
    {
        return $device->owner_id === $user->id && $user->is_admin;
    }
}
