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
use App\Http\Requests\StoreFieldRequest;
use App\Http\Requests\UpdateFieldRequest;
use App\Http\Resources\Api\v1\FieldResource;
use App\Models\ContactList;
use App\Models\Field;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Contact Lists', description: 'Contact Lists')]
class FieldController extends Controller
{
    /**
     * List all fields for a contact list
     *
     * Returns a list of fields for a contact list. The fields are returned sorted by creation date, with the most recently created field appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(FieldResource::class, Field::class, collection: true, paginate: 50)]
    public function index(ContactList $contactList): AnonymousResourceCollection
    {
        Gate::authorize('view', $contactList);

        $devices = $contactList->fields()->latest()->paginate(50)->withQueryString();

        return FieldResource::collection($devices);
    }

    /**
     * Create a field for a contact list
     */
    #[ResponseFromApiResource(FieldResource::class, Field::class, 201, 'Created')]
    public function store(StoreFieldRequest $request, ContactList $contactList): JsonResponse
    {
        $field = $contactList->fields()->create($request->validated());

        return new JsonResponse(
            FieldResource::make($field),
            201,
            ['Location' => route('api.v1.fields.show', $field)]
        );
    }

    /**
     * Retrieve a field
     *
     * Retrieves a Field object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(FieldResource::class, Field::class)]
    public function show(Field $field): FieldResource
    {
        Gate::authorize('view', $field);

        return FieldResource::make($field);
    }

    /**
     * Update a field
     *
     * Updates the specific field by setting the values of the parameters passed. It is recommended to retrieve the field first to get the field's current state because partial updates are not supported.
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateFieldRequest $request, Field $field): JsonResponse
    {
        $field->update($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a field
     *
     * Permanently deletes a field. Also deletes all associated field values of contacts. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Field $field): JsonResponse
    {
        Gate::authorize('delete', $field);

        $field->delete();

        return new JsonResponse(null, 204);
    }
}
