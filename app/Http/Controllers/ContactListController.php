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
use App\Enums\FieldType;
use App\Http\Requests\StoreContactListRequest;
use App\Http\Requests\UpdateContactListRequest;
use App\Http\Resources\ContactListResource;
use App\Http\Resources\FieldResource;
use App\Models\ContactList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class ContactListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return DataTable::make(Auth::user()->contactLists())
                        ->search(function ($query, $search) {
                            $query->where('name', 'like', "%{$search}%");
                        })
                        ->sort(['id', 'name'])
                        ->render('ContactLists/Index', fn($data) => [
                            'lists' => fn() => ContactListResource::collection($data()),
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactListRequest $request): RedirectResponse
    {
        $contactList = Auth::user()->contactLists()->create($request->validated());

        $contactList->fields()->create([
            'label' => __('ui.field.name'),
            'tag' => 'name',
            'type' => FieldType::Text,
            'required' => false
        ]);

        return Redirect::back()->with('success', __('messages.contact_list.created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(ContactList $contactList): Response
    {
        Gate::authorize('view', $contactList);

        return DataTable::make($contactList->fields())
                        ->search(function ($query, $search) {
                            $query->where('fields.label', 'like', "%{$search}%");
                        })
                        ->sort(['label', 'tag', 'type', 'default_value', 'required'])
                        ->render('ContactLists/Edit', fn($data) => [
                            'contactList' => fn() => $contactList,
                            'fields' => fn() => FieldResource::collection($data()),
                        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactListRequest $request, ContactList $contactList): RedirectResponse
    {
        $contactList->update($request->validated());

        return Redirect::back()->with('success', __('messages.contact_list.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('delete', $contactList);

        $contactList->delete();

        return Redirect::back()->with('success', __('messages.contact_list.deleted'));
    }
}
