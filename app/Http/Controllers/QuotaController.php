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

use App\Http\Requests\UpdateQuotaRequest;
use App\Models\Quota;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class QuotaController extends Controller
{
    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuotaRequest $request, Quota $quota): RedirectResponse
    {
        if ($request->boolean('enabled')) {
            $reset_at = transform($request->input('reset_at'), fn($date) => Carbon::parse($date, $request->input('timezone')));
            if (is_null($reset_at)) {
                if ($quota->frequency === $request->integer('frequency') && $quota->frequency_unit === $request->input('frequency_unit')) {
                    $reset_at = $quota->reset_at;
                } else {
                    $reset_at = now()->add($request->input('frequency_unit'), $request->integer('frequency'));
                }
            }

            $available = $quota->available;
            if ($quota->value !== $request->integer('value')) {
                $available = $quota->value > $request->integer('value')
                    ? $available - ($quota->value - $request->integer('value'))
                    : $available + ($request->integer('value') - $quota->value);
            }

            $quota->update([
                ...$request->safe()->only('value', 'frequency', 'frequency_unit', 'enabled'),
                'reset_at' => $reset_at->utc(),
                'available' => max($available, 0),
            ]);
        } else {
            $quota->update(['enabled' => false]);
        }

        return Redirect::back()->with('success', __('messages.quota.updated'));
    }
}
