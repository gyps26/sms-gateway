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

use App\Data\Api\v1\App\UpdateUssdPullData;
use App\Enums\UssdPullStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\App\UssdPullResource;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\UssdPull;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class UssdPullController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Device $device, Campaign $campaign): AnonymousResourceCollection|JsonResponse
    {
        Gate::authorize('app', $device);

        $ussdPulls = $campaign->ussdPulls()
                              ->select(['id', 'code', 'sent_at', 'sim_id'])
                              ->whereIn('sim_id', $device->sims->pluck('id'))
                              ->where('status', UssdPullStatus::Pending)
                              ->paginate(100);

        $credits = 0;

        $reverse = $ussdPulls->getCollection()->reverse();

        foreach ($reverse as $index => $ussdPull) {
            $amount = $credits + config('saas.credits.ussd_pull.amount');

            if ($campaign->user->canConsume($amount, 'credits')->allowed()) {
                $credits = $amount;

                $ussdPull->status = UssdPullStatus::Queued;
                $ussdPull->setRelation('sim', $device->sims->find($ussdPull->sim_id));
            } else {
                $ussdPulls->getCollection()->forget($index);
            }
        }

        if ($ussdPulls->count() > 0) {
            if ($campaign->user->consume($credits)) {
                $ussdPulls->toQuery()->update(['status' => UssdPullStatus::Queued]);
            } else {
                return new JsonResponse(['error' => __('messages.global.limit_exceeded')], 403);
            }
        }

        return UssdPullResource::collection($ussdPulls);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUssdPullData $data, Device $device, UssdPull $ussdPull): JsonResponse
    {
        $ussdPull->update($data->toArray());

        return new JsonResponse(null, 204);
    }
}
