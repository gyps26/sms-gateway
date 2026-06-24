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

namespace App\Http\Controllers\Api\v1;

use App\Enums\FieldType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactListRequest;
use App\Http\Requests\UpdateContactListRequest;
use App\Http\Resources\Api\v1\ContactListResource;
use App\Models\ContactList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Contact Lists', description: 'Contact Lists')]
class ContactListController extends Controller
{
    /**
     * List all contact lists
     *
     * Returns a list of contact lists. The contact lists are returned sorted by creation date, with the most recently created contact list appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(ContactListResource::class, ContactList::class, collection: true, paginate: 10)]
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', ContactList::class);

        $contactLists = Auth::user()->contactLists()->latest()->paginate(10)->withQueryString();

        return ContactListResource::collection($contactLists);
    }

    /**
     * Create a contact list
     */
    #[ResponseFromApiResource(ContactListResource::class, ContactList::class, 201, 'Created')]
    public function store(StoreContactListRequest $request): JsonResponse
    {
        $contactList = Auth::user()->contactLists()->create($request->validated());

        $contactList->fields()->create([
            'label' => __('ui.field.name'),
            'tag' => 'name',
            'type' => FieldType::Text,
            'required' => false
        ]);

        return new JsonResponse(
            ContactListResource::make($contactList),
            201,
            ['Location' => route('api.v1.contact-lists.show', $contactList)]
        );
    }

    /**
     * Retrieve a contact list
     *
     * Retrieves a Contact List object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(name: ContactListResource::class, model: ContactList::class)]
    public function show(ContactList $contactList): ContactListResource
    {
        Gate::authorize('view', $contactList);

        return ContactListResource::make($contactList);
    }

    /**
     * Update a contact list
     *
     * Updates the specific contact list by setting the values of the parameters passed.
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateContactListRequest $request, ContactList $contactList): JsonResponse
    {
        $contactList->update($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a contact list
     *
     * Permanently deletes the specific contact list. Also deletes all contacts and fields associated with it. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(ContactList $contactList): JsonResponse
    {
        Gate::authorize('delete', $contactList);

        $contactList->delete();

        return new JsonResponse(null, 204);
    }
}
