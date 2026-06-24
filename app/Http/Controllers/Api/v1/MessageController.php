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

use App\Data\Filters\MessageFiltersData;
use App\Data\SendCampaignData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\v1\SendMessagesRequest;
use App\Http\Resources\Api\v1\CampaignResource;
use App\Http\Resources\Api\v1\MessageResource;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Messages', description: 'Messages')]
class MessageController extends Controller
{
    /**
     * List all messages
     *
     * Returns a list of messages. The messages are returned sorted by sent date, with the most recently sent message appearing first.
     */
    #[ResponseFromApiResource(MessageResource::class, Message::class, collection: true, paginate: 100)]
    public function index(MessageFiltersData $filters): AnonymousResourceCollection
    {
        $messages = Message::with(['media', 'campaign.media'])
                           ->filter($filters)
                           ->latest('delivered_at')
                           ->latest('sent_at')
                           ->paginate(100)
                           ->withQueryString();

        Message::loadPhoneIds($messages->getCollection());

        return MessageResource::collection($messages);
    }

    /**
     * Retrieve a message
     *
     * Retrieves a Message object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(MessageResource::class, Message::class)]
    public function show(Message $message): MessageResource
    {
        Gate::authorize('view', $message);

        Message::loadPhoneIds(collect([$message]));

        return MessageResource::make($message);
    }

    /**
     * Delete a message
     *
     * Permanently deletes a message. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Message $message): JsonResponse
    {
        Gate::authorize('delete', $message);

        $message->delete();

        return new JsonResponse(null, 204);
    }

    /**
     * Send messages
     *
     * Send an SMS or MMS campaign.
     *
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Throwable
     */
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, 201, 'Created')]
    public function send(SendMessagesRequest $request, #[CurrentUser] User $user): JsonResponse
    {
        $payload = $request->validated();

        $sims = $request->validated('sims', []);
        $senderIds = $request->validated('sender_ids', []);

        if (in_array('*', $sims)) {
            $sims = $user->activeSims()->pluck('sims.id')->toArray();
        }

        if (in_array('*', $senderIds)) {
            $senderIds = $user->activeSenderIds()->pluck('sender_ids.id')->toArray();
        }

        if (data_get($payload, 'random_sender')) {
            if (count($sims) > 0 && (empty($senderIds) || random_int(0, 1))) {
                $sims = [Arr::random($sims)];
                $senderIds = [];
            } else {
                $senderIds = [Arr::random($senderIds)];
                $sims = [];
            }
        }

        $payload['sims'] = $sims;
        $payload['sender_ids'] = $senderIds;

        $data = SendCampaignData::from($payload);

        $campaign = $user->createMessageCampaign($data);

        return new JsonResponse(
            CampaignResource::make($campaign),
            201,
            ['Location' => route('api.v1.campaigns.show', $campaign)]
        );
    }
}
