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

namespace App\Http\Controllers\Api\v1;

use App\Data\Filters\UssdPullFiltersData;
use App\Data\SendCampaignData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\SendUssdPullsRequest;
use App\Http\Resources\Api\v1\CampaignResource;
use App\Http\Resources\Api\v1\UssdPullResource;
use App\Models\Campaign;
use App\Models\User;
use App\Models\UssdPull;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'USSD Pulls', description: 'USSD Pulls')]
class UssdPullController extends Controller
{
    /**
     * List all USSD pulls
     *
     * Returns a list of USSD pulls. The USSD pulls are returned sorted by sent date, with the most recently sent USSD pull appearing first.
     */
    #[ResponseFromApiResource(UssdPullResource::class, UssdPull::class, collection: true, paginate: 100)]
    public function index(UssdPullFiltersData $filters): AnonymousResourceCollection
    {
        $ussdPulls = UssdPull::filter($filters)
                             ->latest('received_at')
                             ->latest('sent_at')
                             ->paginate(100)
                             ->withQueryString();

        return UssdPullResource::collection($ussdPulls);
    }

    /**
     * Retrieve a USSD pull
     *
     * Retrieves a USSD Pull object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(UssdPullResource::class, UssdPull::class)]
    public function show(UssdPull $ussdPull): UssdPullResource
    {
        Gate::authorize('view', $ussdPull);

        return UssdPullResource::make($ussdPull);
    }

    /**
     * Delete a USSD pull
     *
     * Permanently deletes a USSD pull. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(UssdPull $ussdPull): JsonResponse
    {
        Gate::authorize('delete', $ussdPull);

        $ussdPull->delete();

        return new JsonResponse(null, 204);
    }

    /**
     * Send USSD pulls
     *
     * Sends USSD Pull campaign.
     *
     * @throws \Throwable
     */
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, 201, 'Created')]
    public function send(SendUssdPullsRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        $payload = $request->validated();

        $sims = $request->validated('sims', []);

        if (in_array('*', $sims)) {
            $sims = $user->activeSims()->pluck('sims.id')->toArray();
        }

        if (data_get('random_sender', $payload)) {
            $sims = [Arr::random($sims)];
        }

        $payload['sims'] = $sims;

        $data = SendCampaignData::from($payload);

        $campaign = $user->createUssdPullCampaign($data);

        return new JsonResponse(
            CampaignResource::make($campaign),
            201,
            ['Location' => route('api.v1.campaigns.show', $campaign)]
        );
    }
}
