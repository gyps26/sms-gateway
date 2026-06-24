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

use App\Enums\SubscriptionStatus;
use App\Mail\SubscriptionCancelled;
use App\Mail\SubscriptionExpired;
use App\Mail\SubscriptionRenewed;
use App\Mail\SubscriptionStarted;
use App\Models\Subscription;
use Illuminate\Support\Facades\Mail;

class SubscriptionObserver
{
    /**
     * Handle the Subscription "creating" event.
     */
    public function creating(Subscription $subscription): void
    {
        if ($subscription->status === SubscriptionStatus::Active || $subscription->status === SubscriptionStatus::Trial) {
            $subscription->features = $subscription->plan->features;
        }
    }

    /**
     * Handle the Subscription "created" event.
     */
    public function created(Subscription $subscription): void
    {
        if ($subscription->status === SubscriptionStatus::Active && class_exists(SubscriptionStarted::class)) {
            Mail::to($subscription->user)
                ->send(new SubscriptionStarted($subscription));
        }
    }

    /**
     * Handle the Subscription "updating" event.
     */
    public function updating(Subscription $subscription): void
    {
        if ($subscription->isDirty('renewal_at') && $subscription->status === SubscriptionStatus::Active) {
            $subscription->features = $subscription->plan->features;
        }
    }

    /**
     * Handle the Subscription "updated" event.
     */
    public function updated(Subscription $subscription): void
    {
        if ($subscription->wasChanged('renewal_at') && $subscription->status === SubscriptionStatus::Active && class_exists(SubscriptionRenewed::class)) {
            Mail::to($subscription->user)
                ->send(new SubscriptionRenewed($subscription));
        }

        if ($subscription->wasChanged('status')) {
            if ($subscription->status === SubscriptionStatus::Cancelled && class_exists(SubscriptionCancelled::class)) {
                Mail::to($subscription->user)
                    ->send(new SubscriptionCancelled($subscription));
            } elseif ($subscription->status === SubscriptionStatus::Expired && class_exists(SubscriptionExpired::class)) {
                Mail::to($subscription->user)
                    ->send(new SubscriptionExpired($subscription));
            }
        }
    }

    /**
     * Handle the Subscription "deleted" event.
     */
    public function deleted(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "restored" event.
     */
    public function restored(Subscription $subscription): void
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     */
    public function forceDeleted(Subscription $subscription): void
    {
        //
    }
}
