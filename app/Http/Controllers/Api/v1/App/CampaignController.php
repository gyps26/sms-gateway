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

namespace App\Http\Controllers\Api\v1\App;

use App\Data\Api\v1\App\StopCampaignData;
use App\Data\Api\v1\App\UpdateCampaignData;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\MessageStatus;
use App\Enums\UssdPullStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\App\CampaignResource;
use App\Models\Campaign;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Device $device): AnonymousResourceCollection
    {
        Gate::authorize('app', $device);

        $campaigns = $device->campaigns()
                            ->where('campaigns.status', CampaignStatus::Processed)
                            ->where(function ($query) {
                                return $query->where('campaignables.status', CampaignableStatus::Pending)
                                             ->orWhere('campaignables.status', CampaignableStatus::Cancelling);
                            })
                            ->get()
                            ->each(function (Campaign $campaign) use ($device) {
                                if ($campaign->pivot->status === CampaignableStatus::Pending) {
                                    $campaign->pivot->status = CampaignableStatus::Queued;
                                    $campaign->pivot->save();
                                }
                            });

        return CampaignResource::collection($campaigns);
    }

    /**
     * Display the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Device $device, Campaign $campaign): CampaignResource
    {
        Gate::authorize('app', $device);

        return CampaignResource::make($campaign);
    }

    /**
     * @throws \Throwable
     */
    public function stop(StopCampaignData $data, Device $device, Campaign $campaign): JsonResponse
    {
        return DB::transaction(function () use ($data, $device, $campaign) {
            if ($campaign->type === CampaignType::UssdPull) {
                $count = $device->ussdPulls()
                                ->whereCampaignId($campaign->id)
                                ->whereIn('ussd_pulls.id', $data->remaining)
                                ->update([
                                    'status' => UssdPullStatus::Pending,
                                    'received_at' => null
                                ]);

                $credits = $count * config('saas.credits.ussd_pull.amount');
            } else {
                $messages = $device->messages()
                                   ->whereCampaignId($campaign->id)
                                   ->whereIn('messages.id', $data->remaining)
                                   ->get(['messages.id', 'messages.content']);

                $messages->toQuery()->update([
                    'status' => MessageStatus::Pending,
                    'delivered_at' => null
                ]);

                $credits = $messages->sum('credits');
            }

            $campaign->user->consume(-$credits);

            return new JsonResponse(null, 204);
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @throws \Throwable
     */
    public function update(UpdateCampaignData $data, Device $device, Campaign $campaign): JsonResponse
    {
        $device->campaigns()->updateExistingPivot($campaign->id, $data->toArray());

        return new JsonResponse(null, 204);
    }
}
