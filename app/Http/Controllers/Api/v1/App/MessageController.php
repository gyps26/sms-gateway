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

use App\Data\Api\v1\App\StoreReceivedMessageData;
use App\Data\Api\v1\App\UpdateMessageData;
use App\Enums\MessageStatus;
use App\Enums\MessageType;
use App\Events\MessageReceived;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\v1\App\MessageResource;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\Message;
use App\Models\Quota;
use App\Models\Setting;
use App\Models\Sim;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Device $device, Campaign $campaign): JsonResponse
    {
        Gate::authorize('app', $device);

        $messages = $campaign->messages()
                             ->select(['id', 'to', 'content', 'type', 'messageable_id'])
                             ->whereMorphedTo('messageable', Sim::class)
                             ->whereIn('messageable_id', $campaign->pivot->senders['sims'])
                             ->where('status', MessageStatus::Pending)
                             ->oldest('id')
                             ->paginate(100);

        if ($messages->getCollection()->count() > 0) {
            $campaign->pivot->load('sims.quota');

            $user = $campaign->user_id === Auth::id() ? Auth::user() : $campaign->user;

            $quotas = $campaign->pivot->sims->pluck('quota', 'id');

            $outOfQuota = $quotas->every(fn(Quota $quota) => $quota->enabled && $quota->available <= 0);

            if ($outOfQuota) {
                return new JsonResponse(null, 503, ['Retry-After' => $quotas->min('reset_at')]);
            }

            $reverse = $messages->getCollection()->reverse();

            $deliveredAt = Carbon::now('UTC')->format('Y-m-d H:i:s');

            $credits = 0;
            $waitForConfirmation = Setting::retrieve('messaging.sim.wait_for_confirmation', Auth::id(), config('messaging.sim.wait_for_confirmation'));
            foreach ($reverse as $index => $message) {
                $amount = $credits + $message->credits;

                $quota = $quotas->get($message->messageable_id);
                $allowed = $user->canConsume($amount, 'credits')->allowed();

                if ($allowed && $quota->enabled) {
                    if ($quota->available > 0) {
                        $quota->available--;
                    } else {
                        $allowed = false;
                    }
                }

                if ($allowed) {
                    $credits = $amount;

                    $message->status = MessageStatus::Queued;
                    $message->delivered_at = $deliveredAt;
                    $message->wait_for_confirmation = $waitForConfirmation;
                    $message->setRelation('sim', $campaign->pivot->sims->find($message->messageable_id));
                    $message->setRelation('campaign', $campaign);
                } else {
                    $messages->getCollection()->forget($index);
                }
            }

            if ($messages->count() > 0) {
                if ($user->consume($credits)) {
                    $messages->toQuery()->update([
                        'status' => MessageStatus::Queued,
                        'delivered_at' => $deliveredAt
                    ]);

                    $quotas->each(fn($quota) => $quota->update());
                } else {
                    return new JsonResponse(['error' => __('messages.global.limit_exceeded')], 403);
                }
            }
        }

        return MessageResource::collection($messages)->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMessageData $data, Device $device, Message $message): JsonResponse
    {
        $message->update([
            'status' => $data->status,
            'delivered_at' => $data->deliveredAt,
            'response' => array_filter(
                $data->only('resultCode', 'errorCode')->toArray(),
                fn($value) => isset($value)
            )
        ]);

        return new JsonResponse(null, 204);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Throwable
     */
    public function store(StoreReceivedMessageData $data, Device $device): JsonResponse
    {
        DB::transaction(function () use ($device, $data) {
            if ($device->users()->count() === 2) {
                $user = $device->users()->whereNot('users.id', Auth::id())->first();
            } else {
                $user = Auth::user();
            }

            $credits = config('saas.credits.received.amount');

            if (! $user->consume($credits)) {
                throw new AuthorizationException(__('messages.global.limit_exceeded'));
            }

            $sim = $device->sims->firstWhere('slot', $data->simSlot);

            $messageData = [
                'from' => $data->mobileNumber,
                'to' => $sim->fromAddress(),
                'status' => MessageStatus::Received,
                'type' => $data->type,
                'content' => $data->content,
                'messageable_id' => $sim->id,
                'messageable_type' => $sim->getMorphClass(),
                'sent_at' => $data->sentAt,
                'delivered_at' => $data->receivedAt
            ];

            $message = $user->messages()->create($messageData);

            if ($message->type === MessageType::Mms && $data->attachments) {
                foreach ($data->attachments as $attachment) {
                    $message->addMedia($attachment)->toMediaCollection('attachments');
                }
            }

            MessageReceived::dispatch($message, $user);
        });

        return new JsonResponse(null, 201);
    }
}
