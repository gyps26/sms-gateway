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

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\Api\v1\ContactResource;
use App\Models\Contact;
use App\Models\ContactList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Contacts', description: 'Contacts')]
class ContactController extends Controller
{
    /**
     * List all contacts for a contact list
     *
     * Returns a list of contacts for a contact list. The contacts are returned sorted by creation date, with the most recently created contact appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(ContactResource::class, Contact::class, collection: true, with: ['contactFields'], paginate: 100)]
    public function index(ContactList $contactList): AnonymousResourceCollection
    {
        Gate::authorize('view', $contactList);

        $contacts = $contactList->contacts()->with('contactFields')->latest()->paginate(100)->withQueryString();

        foreach ($contacts->items() as $contact) {
            $contact->setRelation('contactList', $contactList);
        }

        return ContactResource::collection($contacts);
    }

    /**
     * Create a contact in a contact list
     *
     * @throws \Throwable
     */
    #[ResponseFromApiResource(ContactResource::class, Contact::class, 201, 'Created')]
    public function store(StoreContactRequest $request, ContactList $contactList): JsonResponse
    {
        $contact = $contactList->addContact($request->validated());

        return new JsonResponse(
            ContactResource::make($contact),
            201,
            ['Location' => route('api.v1.contacts.show', $contact)]
        );
    }

    /**
     * Retrieve a contact
     *
     * Retrieves a Contact object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(ContactResource::class, Contact::class, with: ['contactFields'])]
    public function show(Contact $contact): ContactResource
    {
        Gate::authorize('view', $contact);

        return ContactResource::make($contact->load(['contactList', 'contactFields']));
    }

    /**
     * Update a contact
     *
     * Update a contact in a contact list with the given data. It is recommended to retrieve the contact first to get the contact's current state because partial updates are not supported.
     *
     * @throws \Throwable
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateContactRequest $request, Contact $contact): JsonResponse
    {
        $contact->modify($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a contact
     *
     * Permanently deletes a contact. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Contact $contact): JsonResponse
    {
        Gate::authorize('delete', $contact);

        $contact->delete();

        return new JsonResponse(null, 204);
    }
}
