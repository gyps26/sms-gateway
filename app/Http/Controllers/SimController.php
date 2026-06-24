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
use App\Helpers\Timezone;
use App\Http\Requests\UpdateSimRequest;
use App\Http\Resources\SimResource;
use App\Models\Sim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class SimController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Exception
     */
    public function index(): Response|RedirectResponse
    {
        return
            DataTable::make(Auth::user()->sims()->whereActive(true)->with(['quota', 'device']))
                     ->search(fn($query, $search) => $query->search($search))
                     ->sort(
                         ['id', 'name', 'country', 'carrier', 'slot', 'battery', 'is_charging', 'signal_strength', 'active'],
                         ['id' => 'sims.id', 'name' => 'sims.name']
                     )
                     ->render('Sims/Index', fn($data) => [
                         'sims' => fn() => SimResource::collection($data()),
                         'timezones' => fn() => Timezone::all()
                     ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSimRequest $request, Sim $sim): RedirectResponse
    {
        $sim->update($request->validated());

        return Redirect::back()->with('success', __('messages.sim.updated'));
    }
}
