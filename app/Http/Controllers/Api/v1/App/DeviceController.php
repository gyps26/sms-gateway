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

namespace App\Http\Controllers\Api\v1\App;

use App\Data\Api\v1\App\DeviceData;
use App\Data\Api\v1\App\RegisterDeviceData;
use App\Facades\JwtAuth;
use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use PragmaRX\Google2FA\Google2FA;

class DeviceController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(DeviceData $data, Device $device): JsonResponse
    {
        $device->update($data->except('sims')->toArray());

        $device->updateOrCreateSims($data->sims->toArray());

        return new JsonResponse(null, 204);
    }

    /**
     * Register a new device or log in to an existing one.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException
     * @throws \PragmaRX\Google2FA\Exceptions\InvalidCharactersException
     * @throws \PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException
     */
    public function register(RegisterDeviceData $data): JsonResponse
    {
        if ($data->email) {
            if (! Auth::guard('web')->once(['email' => $data->email, 'password' => $data->password])) {
                return new JsonResponse(['message' => __('messages.device.register.invalid_credentials')], 401);
            }
        } else {
            $subject = JwtAuth::parseToken($data->token);

            if (is_null($subject) || ! Auth::onceUsingId($subject)) {
                return new JsonResponse(['message' => __('messages.device.register.invalid_qr_code')], 401);
            }
        }

        if (Auth::user()->two_factor_secret) {
            if ($data->authCode) {
                $google2fa = new Google2FA();
                if (! $google2fa->verifyKey(decrypt(Auth::user()->two_factor_secret),
                    $data->authCode)) {
                    return new JsonResponse(
                        data: ['message' => __('messages.device.register.2fa.incorrect')],
                        status: 401,
                        headers: ['X-2FA-Required' => 'yes']
                    );
                }
            } else {
                return new JsonResponse(
                    data: ['message' => __('messages.device.register.2fa.required')],
                    status: 401,
                    headers: ['X-2FA-Required' => 'yes']
                );
            }
        }

        Gate::authorize('create', Device::class);

        $device = Device::query()->updateOrCreate(
            ['android_id' => $data->androidId, 'owner_id' => Auth::id()],
            [...$data->device->except('sims')->toArray(), 'enabled' => true]
        );

        when($data->device->sims, fn($sims) => $device->updateOrCreateSims($sims->toArray()));

        $token = JwtAuth::createToken($device);

        return new JsonResponse(['device_id' => $device->id, 'token' => $token,             'license_code' => config('app.license_code')]);
    }

    /**
     * Login to the device.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function login(Device $device): JsonResponse
    {
        Gate::authorize('app', $device);

        $device->update(['enabled' => true]);

        return new JsonResponse(null, 204);
    }

    /**
     * Logout of the device.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function logout(Device $device): JsonResponse
    {
        Gate::authorize('app', $device);

        $device->update(['enabled' => false]);

        return new JsonResponse(null, 204);
    }
}
