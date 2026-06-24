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
use App\Http\Requests\StoreTemplateRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class TemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        return DataTable::make(Auth::user()->templates())
                        ->search(function ($query, $search) {
                            $query->where('templates.name', 'like', "%{$search}%");
                        })
                        ->sort(['name'])
                        ->render('Templates/Index', fn($data) => [
                            'templates' => TemplateResource::collection($data())
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTemplateRequest $request): RedirectResponse
    {
        Auth::user()->templates()->create($request->validated());

        return Redirect::back()->with(['success' => __('messages.template.created')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTemplateRequest $request, Template $template): RedirectResponse
    {
        $template->update($request->validated());

        return Redirect::back()->with(['success' => __('messages.template.updated')]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Template $template): RedirectResponse
    {
        Gate::authorize('delete', $template);

        $template->delete();

        return Redirect::back()->with(['success' => __('messages.template.deleted')]);
    }
}
