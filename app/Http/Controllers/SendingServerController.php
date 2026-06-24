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

use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Helpers\Timezone;
use App\Http\Requests\StoreSendingServerRequest;
use App\Http\Requests\UpdateSendingServerRequest;
use App\Http\Resources\SendingServerResource;
use App\Models\Campaign;
use App\Models\MessageGateway;
use App\Models\SendingServer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SendingServerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function index(): Response|RedirectResponse
    {
        Gate::authorize('viewAny', SendingServer::class);

        return DataTable::make(Auth::user()->sendingServers()->with('quota'))
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->sort(['name'])
                        ->render('SendingServers/Index', fn($data) => [
                            'sendingServers' => fn() => SendingServerResource::collection($data()),
                            'drivers' => fn() => MessageGateway::all(),
                            'timezones' => fn() => Timezone::all()
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSendingServerRequest $request): RedirectResponse
    {
        Auth::user()->sendingServers()->create($request->validated());

        return Redirect::back()->with('success', __('messages.sending_server.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSendingServerRequest $request, SendingServer $sendingServer): RedirectResponse
    {
        $sendingServer->update($request->validated());

        return Redirect::back()->with('success', __('messages.sending_server.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(SendingServer $sendingServer): RedirectResponse
    {
        Gate::authorize('delete', $sendingServer);

        $sendingServer->delete();

        return Redirect::back()->with('success', __('messages.sending_server.deleted'));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancelCampaign(Campaign $campaign, SendingServer $sendingServer): RedirectResponse
    {
        Gate::authorize('cancel', [$campaign, $sendingServer]);

        $status = match ($sendingServer->pivot->status) {
            CampaignableStatus::Stalled, CampaignableStatus::Pending => CampaignableStatus::Cancelled,
            default => CampaignableStatus::Cancelling
        };

        $campaign->sendingServers()->updateExistingPivot($sendingServer->id, [
            'status' => $status,
            'resume_at' => null
        ]);

        if ($status === CampaignableStatus::Cancelled) {
            return Redirect::back()->with('success', __('messages.sending-server.campaign.cancelled'));
        } else {
            return Redirect::back()->with('success', __('messages.sending-server.campaign.cancelling'));
        }
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function retryCampaign(Campaign $campaign, SendingServer $sendingServer): RedirectResponse
    {
        Gate::authorize('retry', [$campaign, $sendingServer]);

        $campaign->sendingServers()
                 ->updateExistingPivot($sendingServer->id, [
                     'status' => CampaignableStatus::Pending,
                     'resume_at' => null
                 ]);

        $campaign->update(['status' => CampaignStatus::Processed]);

        $sendingServer->send($campaign);

        return Redirect::back()->with('success', __('messages.campaign.retried'));
    }

    public function webhook(Request $request, SendingServer $sendingServer): SymfonyResponse
    {
        $messageGateway = MessageGateway::find($sendingServer->driver);

        /** @var \App\Contracts\MessageGateway $gatewayClass */
        $gatewayClass = new $messageGateway->class($sendingServer->config->all());
        return $gatewayClass->handleWebhook($request);
    }
}
