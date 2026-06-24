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
use App\Http\Requests\StoreBlacklistRequest;
use App\Http\Resources\BlacklistResource;
use App\Models\Scopes\MobileNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class BlacklistController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        return DataTable::make(Auth::user()->blacklist())
                        ->search(fn($query, $search) => $query->tap(new MobileNumber($search)))
                        ->sort(['mobile_number', 'created_at'])
                        ->render('Blacklist/Index', fn($data) => [
                            'blacklist' => BlacklistResource::collection($data())
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlacklistRequest $request): RedirectResponse
    {
        $values = [];

        foreach ($request->input('mobile_numbers') as $mobile_number) {
            $values[] = ['mobile_number' => $mobile_number];
        }

        Auth::user()->blacklist()->createMany($values);

        return Redirect::back()->with('success', trans_choice('messages.blacklist.created', count($values)));
    }

    /**
     * Remove the specified resources from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(): RedirectResponse
    {
        $count = DataTable::make(Auth::user()->blacklist())
                          ->search(fn($query, $search) => $query->tap(new MobileNumber($search)))
                          ->query()
                          ->delete();

        if ($count > 0) {
            return Redirect::back()->with('success', trans_choice('messages.blacklist.delete.success', $count));
        }

        return Redirect::back()->with('error', __('messages.blacklist.delete.failed'));
    }
}
