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

use App\Data\Filters\WebhookCallFiltersData;
use App\DataTable;
use App\Http\Resources\WebhookCallResource;
use App\Models\WebhookCall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class WebhookCallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(WebhookCallFiltersData $filters): Response|RedirectResponse
    {
        return DataTable::make(Auth::user()->webhookCalls()->with('webhook')->filter($filters))
                        ->search(function ($query, $search) {
                            $query->whereHas('webhook', function ($query) use ($search) {
                                $query->where('url', $search);
                            });
                        })
                        ->sort(['event', 'status', 'status_code', 'attempts', 'last_retry_at', 'created_at'])
                        ->render('Webhooks/Calls', fn($data) => [
                            'webhookCalls' => fn() => WebhookCallResource::collection($data()),
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Resend webhook call.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function retry(WebhookCall $webhookCall): RedirectResponse
    {
        Gate::authorize('resend', $webhookCall);

        $webhookCall->resend();

        return Redirect::back()->with('success', __('messages.webhook_call.resent'));
    }
}
