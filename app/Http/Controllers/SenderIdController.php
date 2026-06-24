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

use App\Data\ShareSenderIdData;
use App\DataTable;
use App\Http\Requests\StoreSenderIdRequest;
use App\Http\Resources\SenderIdResource;
use App\Models\SenderId;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class SenderIdController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        $sendingServers = fn() => Auth::user()->sendingServers()->whereEnabled(true)->get(['id', 'name']);

        $query = Auth::user()
                     ->senderIds()
                     ->select([
                         'sender_ids.id',
                         'sender_ids.value',
                         'sender_ids.sending_server_id',
                     ]);

        $relations = [
            'sendingServer:id,name,user_id',
        ];

        $users = fn() => [];
        if (Auth::user()->is_admin) {
            $users = fn() => User::whereIsAdmin(false)->get(['id', 'name', 'email']);

            $relations['senderIdUsers'] = fn($query) => $query->whereNot('user_id', Auth::id());
        }

        return DataTable::make($query->with($relations))
                        ->search(fn($query, $search) => $query->where('value', 'like', "%{$search}%"))
                        ->sort(['id', 'value', 'created_at'], ['created_at' => 'sender_id_user.created_at'], 'sender_id_user.created_at')
                        ->render('SenderIds/Index', fn($data) => [
                            'sendingServers' => $sendingServers,
                            'users' => $users,
                            'senderIds' => SenderIdResource::collection($data())
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSenderIdRequest $request): RedirectResponse
    {
        SenderId::create([
            'value' => $request->validated('value'),
            'sending_server_id' => $request->validated('sending_server')
        ]);

        return Redirect::back()->with('success', __('messages.sender_id.created'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(SenderId $senderId): RedirectResponse
    {
        Gate::authorize('delete', $senderId);

        $senderId->delete();

        return Redirect::back()->with('success', __('messages.sender_id.deleted'));
    }

    /**
     * Share the specified resource with another user.
     */
    public function share(ShareSenderIdData $data, SenderId $senderId): RedirectResponse
    {
        $senderId->users()->sync($data->users);

        return Redirect::back()->with('success', __('messages.sender_id.shared'));
    }
}
