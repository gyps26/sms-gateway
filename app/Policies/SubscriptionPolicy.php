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

use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubscriptionPolicy
{
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
    public function view(User $user, Subscription $subscription): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Plan $plan): Response
    {
        if ($user->is_admin) {
            return Response::deny();
        }

        if ($plan->enabled === false) {
            return Response::deny(__('messages.plan.disabled'));
        }

        $exist = $user->subscriptions()->whereStatus(SubscriptionStatus::Active)->exists();
        if ($exist) {
            return Response::deny(__('messages.plan.alreadySubscribed'));
        }

        if ($plan->criteria($user)->sum() > 0) {
            return Response::deny(__('messages.plan.downgradeNotAllowed'));
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Subscription $subscription): bool
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Subscription $subscription): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Subscription $subscription): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Subscription $subscription): bool
    {
        //
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        return ($subscription->status === SubscriptionStatus::Active || $subscription->ends_at === null || $subscription->ends_at > now())
            && ($user->id === $subscription->user_id || $user->is_admin);
    }

    /**
     * Determine whether the user can assign a plan to another user.
     */
    public function assign(User $user, User $model, Plan $plan): Response
    {
        if ($user->is_admin) {
            if ($model->is_admin) {
                return Response::deny();
            }

            $exist = $model->subscriptions()->whereStatus(SubscriptionStatus::Active)->exists();

            if ($exist) {
                return Response::deny(__('messages.plan.alreadySubscribed'));
            }

            if ($plan->criteria($model)->sum() > 0) {
                return Response::deny(__('messages.plan.downgradeNotAllowed'));
            }

            return Response::allow();
        }

        return Response::deny();
    }

    public function subscribe(User $user): bool
    {
        return ! $user->is_admin;
    }
}
