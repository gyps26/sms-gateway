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

use App\Data\ShareDeviceData;
use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Resources\DeviceResource;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class DeviceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response|RedirectResponse
    {
        $builder = Auth::user()->devices();

        $users = fn() => [];
        if (Auth::user()->is_admin) {
            $users = fn() => User::whereIsAdmin(false)->get(['id', 'name', 'email']);
            $builder->with(['deviceUsers' => fn($query) => $query->whereNot('user_id', Auth::id())]);
        }

        return DataTable::make($builder)
                        ->search(fn($query, $search) => $query->search($search))
                        ->sort(['name', 'model'], default: 'device_user.created_at')
                        ->render('Devices/Index', fn($data) => [
                            'devices' => fn() => DeviceResource::collection($data()),
                            'users'   => $users
                        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeviceRequest $request, Device $device): RedirectResponse
    {
        $device->update($request->validated());

        return Redirect::back()->with('success', __('messages.device.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Device $device): RedirectResponse
    {
        Gate::authorize('delete', $device);

        $device->delete();

        return Redirect::back()->with('success', __('messages.device.deleted'));
    }

    /**
     * Share the specified resource with another user.
     */
    public function share(ShareDeviceData $data, Device $device): RedirectResponse
    {
        $device->users()->sync($data->users);

        return Redirect::back()->with('success', __('messages.device.shared'));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancelCampaign(Campaign $campaign, Device $device): RedirectResponse
    {
        Gate::authorize('cancel', [$campaign, $device]);

        $status = match ($device->pivot->status) {
            CampaignableStatus::Stalled, CampaignableStatus::Pending => CampaignableStatus::Cancelled,
            default => CampaignableStatus::Cancelling
        };

        $campaign->devices()->updateExistingPivot($device->id, [
            'status' => $status,
            'resume_at' => null
        ]);

        if ($status === CampaignableStatus::Cancelled) {
            return Redirect::back()->with('success', __('messages.device.campaign.cancelled'));
        } else {
            return Redirect::back()->with('success', __('messages.device.campaign.cancelling'));
        }
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function retryCampaign(Campaign $campaign, Device $device): RedirectResponse
    {
        Gate::authorize('retry', [$campaign, $device]);

        $campaign->devices()
                 ->updateExistingPivot($device->id, [
                     'status' => CampaignableStatus::Pending,
                     'resume_at' => null
                 ]);

        $campaign->update(['status' => CampaignStatus::Processed]);

        $device->send($campaign);

        return Redirect::back()->with('success', __('messages.campaign.retried'));
    }
}
