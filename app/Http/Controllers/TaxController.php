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

use App\Data\Filters\TaxFiltersData;
use App\DataTable;
use App\Http\Requests\StoreTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Http\Resources\CountryFilterResource;
use App\Http\Resources\TaxResource;
use App\Models\Tax;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use PrinsFrank\Standards\Country\CountryAlpha2;

class TaxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(TaxFiltersData $filters): Response|RedirectResponse
    {
        Gate::authorize('viewAny', Tax::class);

        return DataTable::make(Tax::when($filters->country, fn($q, $country) => $q->whereCountry($country)))
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->sort(['name', 'rate', 'country', 'inclusive', 'enabled'])
                        ->render('Taxes/Index', fn($data) => [
                            'taxes' => fn() => TaxResource::collection($data()),
                            'countries' => fn() => CountryFilterResource::collection(CountryAlpha2::cases()),
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaxRequest $request): RedirectResponse
    {
        Tax::create($request->validated());

        return Redirect::back()->with('success', __('messages.tax.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaxRequest $request, Tax $tax): RedirectResponse
    {
        $tax->update($request->validated());

        return Redirect::back()->with('success', __('messages.tax.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Tax $tax): RedirectResponse
    {
        Gate::authorize('delete', $tax);

        $tax->delete();

        return Redirect::back()->with('success', __('messages.tax.deleted'));
    }
}
