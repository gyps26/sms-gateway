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
use App\Enums\SubscriptionStatus;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(): Response|RedirectResponse
    {
        Gate::authorize('viewAny', User::class);

        return DataTable::make(
                            User::whereIsAdmin(false)
                                ->withCount(['calls', 'messages', 'ussdPulls', 'devices', 'sendingServers'])
                        )
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%")
                                  ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->sort(
                            ['id', 'name', 'email', 'email_verified_at', 'calls', 'campaigns', 'devices', 'sending_servers', 'created_at'],
                            ['calls' => 'calls_count', 'messages' => 'messages_count', 'ussd_pulls' => 'ussd_pulls_count', 'devices' => 'devices_count', 'sending_servers' => 'sending_servers_count']
                        )
                        ->render('Users/Index', fn($data) => [
                            'users' => fn() => UserResource::collection($data())
                        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'email_verified_at' => now(),
            'password' => Hash::make($request->input('password')),
        ]);

        $planId = config('saas.trial.plan_id');
        if ($planId) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $planId,
                'subscription_id' => Str::uuid(),
                'status' => SubscriptionStatus::Trial,
                'ends_at' => transform(config('saas.trial.duration'), fn($days) => now()->addDays($days))
            ]);
        }

        return Redirect::back()->with('success', __('messages.user.created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user): RedirectResponse
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return Redirect::back()->with('success', __('messages.user.deleted'));
    }
}
