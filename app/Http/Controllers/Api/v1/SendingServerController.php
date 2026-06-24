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

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSendingServerRequest;
use App\Http\Requests\UpdateSendingServerRequest;
use App\Http\Resources\Api\v1\SendingServerResource;
use App\Models\SendingServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Sending Servers', description: 'Sending Servers')]
class SendingServerController extends Controller
{
    /**
     * List all sending servers
     *
     * Returns a list of sending servers. The sending servers are returned sorted by creation date, with the most recently created sending server appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(SendingServerResource::class, SendingServer::class, collection: true, paginate: 100)]
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', SendingServer::class);

        $sendingServers = Auth::user()->sendingServers()->latest()->paginate(100)->withQueryString();

        return SendingServerResource::collection($sendingServers);
    }

    /**
     * Create a sending server
     */
    #[ResponseFromApiResource(SendingServerResource::class, SendingServer::class, 201, 'Created')]
    public function store(StoreSendingServerRequest $request): JsonResponse
    {
        $sendingServer = Auth::user()->sendingServers()->create($request->validated());

        return new JsonResponse(
            SendingServerResource::make($sendingServer),
            201,
            ['Location' => route('api.v1.sending-servers.show', $sendingServer)]
        );
    }

    /**
     * Retrieve a sending server
     *
     * Retrieves a SendingServer object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(SendingServerResource::class, SendingServer::class)]
    public function show(SendingServer $sendingServer): SendingServerResource
    {
        Gate::authorize('view', $sendingServer);

        return SendingServerResource::make($sendingServer);
    }

    /**
     * Update a sending server
     *
     * Update the specific sending server by setting the values of the parameters passed. It is recommended to retrieve the sending server first to get the sending server's current state because partial updates are not supported.
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateSendingServerRequest $request, SendingServer $sendingServer): JsonResponse
    {
        $sendingServer->update($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a sending server
     *
     * Permanently deletes a sending server. Also removes sender ids associated with the sending server and any messages associated with those sender ids. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(SendingServer $sendingServer): JsonResponse
    {
        Gate::authorize('delete', $sendingServer);

        $sendingServer->delete();

        return new JsonResponse(null, 204);
    }
}
