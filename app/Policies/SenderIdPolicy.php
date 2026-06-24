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

use App\Models\SenderId;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SenderIdPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, SenderId $senderId): bool
    {
        return $user->id === $senderId->sendingServer->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $count = 1): Response
    {
        $count += $user->ownedSenderIds()->count();

        return $user->canConsume($count, 'sender_ids');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, SenderId $senderId): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, SenderId $senderId): bool
    {
        return $user->id === $senderId->sendingServer->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, SenderId $senderId): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, SenderId $senderId): bool
    {
        //
    }

    public function share(User $user, SenderId $senderId): bool
    {
        return $senderId->sendingServer->user_id === $user->id;
    }
}
