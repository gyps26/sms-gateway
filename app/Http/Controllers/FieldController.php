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

use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Models\ContactList;
use App\Models\Field;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class FieldController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFieldRequest $request, ContactList $contactList): RedirectResponse
    {
        $contactList->fields()->create($request->validated());

        return Redirect::back()->with('success', __('messages.field.created'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFieldRequest $request, Field $field): RedirectResponse
    {
        $field->update($request->validated());

        return Redirect::back()->with('success', __('messages.field.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Field $field): RedirectResponse
    {
        Gate::authorize('delete', $field);

        $field->delete();

        return Redirect::back()->with('success', __('messages.field.deleted', ['label' => $field->label]));
    }
}
