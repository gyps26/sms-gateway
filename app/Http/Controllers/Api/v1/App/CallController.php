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

use App\Data\Api\v1\App\StoreCallData;
use App\Enums\CallType;
use App\Enums\WebhookEvent;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CallController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @throws \Throwable
     */
    public function store(StoreCallData $data, Device $device): JsonResponse
    {
        DB::transaction(function () use ($device, $data) {
            if ($device->users()->count() === 2) {
                $user = $device->users()->whereNot('users.id', Auth::id())->first();
            } else {
                $user = Auth::user();
            }

            $credits = config('saas.credits.call.amount');

            if (! $user->consume($credits)) {
                throw new AuthorizationException(__('messages.global.limit_exceeded'));
            }

            $sim = $device->sims()->firstWhere('slot', $data->simSlot);

            $call = $user->calls()->create([
                'number' => $data->number,
                'type' => match ($data->type) {
                    1 => CallType::Incoming,
                    2 => CallType::Outgoing,
                    3 => CallType::Missed,
                    4 => CallType::Voicemail,
                    5 => CallType::Rejected,
                    6 => CallType::Blocked,
                    7 => CallType::AnsweredExternally,
                },
                'started_at' => $data->startedAt,
                'duration' => $data->duration,
                'sim_id' => $sim->id
            ]);

            $user->callWebhooks(WebhookEvent::CallAdded, $call, ['user_id']);
        });

        return new JsonResponse(null, 201);
    }
}
