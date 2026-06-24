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
use App\Http\Requests\StorePlanRequest;
use App\Http\Requests\UpdatePlanRequest;
use App\Http\Resources\CurrencyFilterResource;
use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(): Response|RedirectResponse
    {
        Gate::authorize('viewAny', Plan::class);

        return DataTable::make(Plan::query())
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->sort(['name', 'price', 'currency', 'enabled', 'created_at', 'updated_at'])
                        ->render('Plans/Index', fn($data) => [
                            'plans' => fn() => PlanResource::collection($data()),
                            'currencies' => fn() => CurrencyFilterResource::collection(CurrencyAlpha3::cases()),
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePlanRequest $request): RedirectResponse
    {
        Plan::create($request->validated());

        return Redirect::back()->with('success', __('messages.plan.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePlanRequest $request, Plan $plan): RedirectResponse
    {
        $plan->update($request->validated());

        return Redirect::back()->with('success', __('messages.plan.updated'));
    }
}
