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
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\Api\v1\DeviceResource;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group(name: 'Devices', description: 'Devices')]
class DeviceController extends Controller
{
    /**
     * List all devices
     *
     * Returns a list of devices. The devices are returned sorted by creation date, with the most recently created device appearing first.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(DeviceResource::class, Device::class, collection: true, with: ['sims'], paginate: 100)]
    public function index(): AnonymousResourceCollection
    {
        Gate::authorize('viewAny', Device::class);

        $devices = Auth::user()->devices()->with('sims')->latest()->paginate(100)->withQueryString();

        return DeviceResource::collection($devices);
    }

    /**
     * Retrieve a device
     *
     * Retrieves a Device object.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[ResponseFromApiResource(DeviceResource::class, Device::class, with: ['sims'])]
    public function show(Device $device): DeviceResource
    {
        Gate::authorize('view', $device);

        return DeviceResource::make($device->load('sims'));
    }

    /**
     * Update a device
     *
     * Update the specific device by setting the values of the parameters passed.
     */
    #[Response(status: 204, description: 'No content')]
    public function update(UpdateDeviceRequest $request, Device $device): JsonResponse
    {
        $device->update($request->validated());

        return new JsonResponse(null, 204);
    }

    /**
     * Delete a device
     *
     * Permanently deletes a device. Also removes the SIMs associated with the device and any messages, USSD Pulls and call logs associated with those SIMs. It cannot be undone.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    #[Response(status: 204, description: 'No content')]
    public function destroy(Device $device): JsonResponse
    {
        Gate::authorize('delete', $device);

        $device->delete();

        return new JsonResponse(null, 204);
    }
}
