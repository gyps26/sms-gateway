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

use App\Data\Filters\CampaignFiltersData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\Api\v1\CampaignDeviceResource;
use App\Http\Resources\Api\v1\CampaignResource;
use App\Http\Resources\Api\v1\CampaignSendingServerResource;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\SendingServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Campaigns', description: 'Campaigns')]
class CampaignController extends Controller
{
    /**
     * List all campaigns
     *
     * Returns a list of campaigns. The campaigns are returned sorted by creation date, with the most recently created campaign appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, collection: true, paginate: 100)]
    public function index(CampaignFiltersData $filters): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Campaign::class);

        $campaigns = Auth::user()->campaigns()->filter($filters)->latest()->paginate(100)->withQueryString();

        return CampaignResource::collection($campaigns);
    }

    /**
     * List all devices used in a campaign
     *
     * Returns a list of devices used in a campaign. The devices are returned with sims used in the campaign and status of the campaign on the device.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(CampaignDeviceResource::class, Device::class, collection: true, with: ['sims'], paginate: 10)]
    public function devices(Campaign $campaign): AnonymousResourceCollection
    {
        Gate::authorize('view', $campaign);

        $devices = $campaign
            ->devices()
            ->paginate(10)
            ->withQueryString();

        foreach ($devices->items() as $device) {
            $device->load(['sims' => fn($query) => $query->whereIn('id', $device->pivot->senders['sims'])]);
        }

        return CampaignDeviceResource::collection($devices);
    }

    /**
     * List all sending servers used in a campaign
     *
     * Returns a list of sending servers used in a campaign. The sending servers are returned with sender ids used in the campaign and status of the campaign on the sending server.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(CampaignSendingServerResource::class, SendingServer::class, collection: true, with: ['senderIds'], paginate: 10)]
    public function sendingServers(Campaign $campaign): AnonymousResourceCollection
    {
        Gate::authorize('view', $campaign);

        $sendingServers = $campaign
            ->sendingServers()
            ->select(['sending_servers.id', 'sending_servers.name', 'sending_servers.enabled'])
            ->paginate(10)
            ->withQueryString();

        foreach ($sendingServers->items() as $sendingServer) {
            $sendingServer->load(['senderIds' => fn($query) => $query->whereIn('id', $sendingServer->pivot->senders['sender_ids'])]);
        }

        return CampaignSendingServerResource::collection($sendingServers);
    }

    /**
     * Retrieve a campaign
     *
     * Retrieves a Campaign object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class)]
    public function show(Campaign $campaign): CampaignResource
    {
        Gate::authorize('view', $campaign);

        return CampaignResource::make($campaign);
    }

    /**
     * Update a campaign
     *
     * Updates the specific campaign by setting the values of the parameters passed.
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateCampaignRequest $request, Campaign $campaign): JsonResponse
    {
        $campaign->update($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a campaign
     *
     * Permanently deletes a campaign. Also deletes all messages or USSD pulls associated with it. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Campaign $campaign): JsonResponse
    {
        Gate::authorize('delete', $campaign);

        $campaign->delete();

        return new JsonResponse(null, 204);
    }
}
