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

use Illuminate\Support\Facades\Gate;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenObserver
{
    /**
     * Handle the PersonalAccessToken "creating" event.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function creating(PersonalAccessToken $personalAccessToken): void
    {
        Gate::authorize('create', PersonalAccessToken::class);
    }

    /**
     * Handle the PersonalAccessToken "created" event.
     */
    public function created(PersonalAccessToken $personalAccessToken): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "updated" event.
     */
    public function updated(PersonalAccessToken $personalAccessToken): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "deleted" event.
     */
    public function deleted(PersonalAccessToken $personalAccessToken): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "restored" event.
     */
    public function restored(PersonalAccessToken $personalAccessToken): void
    {
        //
    }

    /**
     * Handle the PersonalAccessToken "force deleted" event.
     */
    public function forceDeleted(PersonalAccessToken $personalAccessToken): void
    {
        //
    }
}
