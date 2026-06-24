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
use App\Http\Requests\Api\v1\StoreSenderIdRequest;
use App\Http\Resources\Api\v1\SenderIdResource;
use App\Models\SenderId;
use App\Models\SendingServer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'SenderIds', description: 'SenderIds')]
class SenderIdController extends Controller
{
    /**
     * List all sender ids
     *
     * Returns a list of sender ids. The sender ids are returned sorted by creation date, with the most recently created sender id appearing first.
     */
    #[ResponseFromApiResource(SenderIdResource::class, SenderId::class, collection: true, with: ['sendingServer'], paginate: 100)]
    public function index(): AnonymousResourceCollection
    {
        $senderIds = Auth::user()->senderIds()->latest()->paginate(100)->withQueryString();

        return SenderIdResource::collection($senderIds);
    }

    /**
     * Create a sender id for a sending server
     */
    #[ResponseFromApiResource(SenderIdResource::class, SenderId::class, 201, 'Created', with: ['sendingServer'])]
    public function store(StoreSenderIdRequest $request, SendingServer $sendingServer): JsonResponse
    {
        $senderId = $sendingServer->senderIds()->create(
            $request->validated()
        );

        return new JsonResponse(
            SenderIdResource::make($senderId),
            201,
            ['Location' => route('api.v1.sender-ids.show', $senderId)]
        );
    }

    /**
     * Retrieve a sender id
     *
     * Retrieves a SenderId object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(SenderIdResource::class, SenderId::class, with: ['sendingServer'])]
    public function show(SenderId $senderId): SenderIdResource
    {
        Gate::authorize('view', $senderId);

        return SenderIdResource::make($senderId);
    }

    /**
     * Delete a sender id
     *
     * Permanently deletes a sender id. Also removes any messages sent using this sender id. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(SenderId $senderId): JsonResponse
    {
        Gate::authorize('delete', $senderId);

        $senderId->delete();

        return new JsonResponse(null, 204);
    }
}
