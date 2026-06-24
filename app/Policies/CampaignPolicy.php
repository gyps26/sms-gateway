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

use App\Contracts\Campaignable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
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
    public function view(User $user, Campaign $campaign): bool
    {
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        //
    }

    public function retry(User $user, Campaign $campaign, ?Campaignable $campaignable = null): Response
    {
        if ($campaign->user_id !== $user->id) {
            return Response::deny();
        }

        $campaignables = [CampaignableStatus::Failed, CampaignableStatus::Cancelled];

        if ($campaign->status === CampaignStatus::Completed) {
            if (is_null($campaignable)) {
                $exists = $campaign->campaignables()
                                   ->whereIn('status', $campaignables)
                                   ->exists();

                if ($exists) {
                    return Response::allow();
                }
            } else {
                if (in_array($campaignable->pivot->status, $campaignables)) {
                    return Response::allow();
                }
            }
        }

        return Response::deny(__('messages.campaign.unable_to_retry'));
    }

    public function cancel(User $user, Campaign $campaign, ?Campaignable $campaignable = null): Response
    {
        if ($campaign->user_id !== $user->id) {
            return Response::deny();
        }

        $campaignables = [CampaignableStatus::Pending, CampaignableStatus::Queued, CampaignableStatus::Stalled];

        if (in_array($campaign->status, [CampaignStatus::Processed, CampaignStatus::Scheduled])) {
            if (is_null($campaignable)) {
                $exists = $campaign->campaignables()
                                   ->whereIn('status', $campaignables)
                                   ->exists();

                if ($exists) {
                    return Response::allow();
                }
            } else {
                if (in_array($campaignable->pivot->status, $campaignables)) {
                    return Response::allow();
                }
            }
        }

        return Response::deny(__('messages.campaign.unable_to_cancel'));
    }
}
