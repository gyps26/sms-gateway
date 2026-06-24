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

namespace App\Http\Controllers;

use App\Data\SendCampaignData;
use App\Enums\MessageStatus;
use App\Http\Requests\SendChatMessageRequest;
use App\Http\Requests\ShowChatRequest;
use App\Http\Resources\MessageResource;
use App\Models\Contact;
use App\Models\SenderId;
use App\Models\Sim;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function show(ShowChatRequest $request)
    {
        $mobileNumber = $request->safe()->string('mobile_number');
        $sim = $request->safe()->integer('sim');
        $senderId = $request->safe()->integer('sender_id');

        $messages = [];
        $phoneId = null;

        if ($mobileNumber) {
            $messages = function () use ($mobileNumber, $sim, $senderId) {
                $messages = Auth::user()
                                ->messages()
                                ->with(['media', 'campaign.media'])
                                ->when(
                                    $sim,
                                    function (Builder $query, $sim) {
                                        $query->whereMorphedTo('messageable', Sim::class)
                                              ->whereMessageableId($sim);
                                    },
                                    function (Builder $query) use ($senderId) {
                                        $query->whereMorphedTo('messageable', SenderId::class)
                                              ->whereMessageableId($senderId);
                                    })
                                ->where(function (Builder $query) use ($mobileNumber) {
                                    $query->where(function (Builder $query) use ($mobileNumber) {
                                        $query->whereStatus(MessageStatus::Received)
                                              ->whereFrom($mobileNumber);
                                    })->orWhere(function (Builder $query) use ($mobileNumber) {
                                        $query->whereNot('status', MessageStatus::Received)
                                              ->whereTo($mobileNumber);
                                    });
                                })
                                ->latest('id')
                                ->limit(1000)
                                ->get()
                                ->reverse();

                return MessageResource::collection($messages);
            };

            $phoneId = fn() => Contact::identify($mobileNumber);
        }

        $sims = function () {
            return Auth::user()
                       ->activeSims()
                       ->get(['sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label', 'sims.device_id']);
        };

        $senderIds = function () {
            return Auth::user()
                       ->activeSenderIds()
                       ->with(['sendingServer:id,name,supported_types'])
                       ->get(['sender_ids.id', 'sender_ids.value', 'sender_ids.sending_server_id']);
        };

        return Inertia::render('Chat', [
            'phoneId' => $phoneId,
            'messages' => $messages,
            'senderIds' => $senderIds,
            'sims' => $sims,
            'params' => [
                'mobile_number' => $mobileNumber,
                'sim' => $sim,
                'sender_id' => $senderId,
            ],
        ]);
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Throwable
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function send(SendChatMessageRequest $request, #[CurrentUser] User $user)
    {
        $data = SendCampaignData::from([
            'type' => $request->validated('type'),
            'mobile_numbers' => [$request->validated('mobile_number')],
            'message' => $request->validated('message'),
            'sims' => when($request->validated('sim'), fn($sim) => [$sim], []),
            'senderIds' => when($request->validated('sender_id'), fn($senderId) => [$senderId], []),
            'attachments' => $request->validated('attachments', []),
            'delivery_report' => $request->validated('delivery_report')
        ]);

        $user->createMessageCampaign($data);

        return Redirect::back();
    }
}
