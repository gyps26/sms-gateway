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

use App\Data\Filters\CallFiltersData;
use App\DataTable;
use App\Http\Resources\CallResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class CallController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CallFiltersData $filters): Response|RedirectResponse
    {
        $sims = function () {
            return Auth::user()
                       ->sims()
                       ->whereActive(true)
                       ->with('device:id,name,model')
                       ->get(['sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label', 'sims.device_id'])
                       ->keyBy('id');
        };

        return DataTable::make(Auth::user()->calls()->filter($filters))
                        ->search(fn($query, $search) => $query->search($search))
                        ->sort(['number', 'incoming', 'sim', 'started_at'], default: 'started_at')
                        ->render('Calls/Index', fn($data) => [
                            'calls' => fn() => CallResource::collection($data()),
                            'sims' => $sims,
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(CallFiltersData $filters)
    {
        $count = DataTable::make(Auth::user()->calls()->filter($filters))
                          ->search(fn($query, $search) => $query->search($search))
                          ->query('calls.id')
                          ->delete();

        if ($count > 0) {
            return Redirect::back()->with('success', trans_choice('messages.call.delete.success', $count));
        }

        return Redirect::back()->with('error', __('messages.call.delete.failed'));
    }
}
