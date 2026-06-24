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

use App\Data\Filters\UssdPullFiltersData;
use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Enums\UssdPullStatus;
use App\Http\Resources\UssdPullResource;
use App\Jobs\RetryCampaign;
use App\Models\Campaign;
use App\Models\User;
use App\Models\UssdPull;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;

class UssdPullController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(UssdPullFiltersData $filters): Response|RedirectResponse
    {
        $users = function () {
            $relations = [
                'sims' => function ($query) {
                    $query->select('sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label')
                          ->whereActive(true);
                }
            ];

            if (Auth::user()->is_admin) {
                $users = User::with($relations)->get(['id', 'name', 'email']);
            } else {
                $users = Collection::wrap(Auth::user()->load($relations));
            }

            $users->map(function ($user) {
                $user->setRelation('sims', $user->sims->keyBy('id'));
            });

            return $users->keyBy('id');
        };

        return DataTable::make(UssdPull::filter($filters)->latest('received_at'))
                        ->search(fn($query, $search) => $query->search($search))
                        ->sort(['code', 'received_at', 'sent_at', 'status'], default: 'sent_at')
                        ->render('USSD/Index', fn($data) => [
                            'ussdPulls' => fn() => UssdPullResource::collection($data()),
                            'users' => $users,
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(UssdPullFiltersData $filters): RedirectResponse
    {
        $count = DataTable::make(UssdPull::filter($filters))
                          ->search(fn($query, $search) => $query->search($search))
                          ->query()
                          ->delete();

        if ($count === 0) {
            return Redirect::back()->with('error', __('messages.ussd_pull.delete.failed'));
        }

        return Redirect::back()->with('success', trans_choice('messages.ussd_pull.delete.success', $count));
    }

    /**
     * @throws \Throwable
     */
    public function retry(UssdPullFiltersData $filters): RedirectResponse
    {
        if (is_null($filters->campaign)) {
            RetryCampaign::dispatch(request()->all(), $filters);
            return Redirect::back()->with('success', __('messages.ussd_pull.retry.queued'));
        }

        return DB::transaction(function () use ($filters) {
            $count = DataTable::make(UssdPull::filter($filters))
                              ->search(fn($query, $search) => $query->search($search))
                              ->query()
                              ->whereHas('campaign', fn($query) => $query->where('status', CampaignStatus::Completed))
                              ->update([
                                  'status' => UssdPullStatus::Pending,
                                  'sent_at' => now(),
                                  'received_at' => null,
                                  'response' => null
                              ]);

            if ($count <= 0) {
                return Redirect::back()->with('error', __('messages.ussd_pull.retry.failed'));
            }

            $campaign = Campaign::findOrFail($filters->campaign);

            $campaign->campaignables()->update(['status' => CampaignableStatus::Pending]);

            $campaign->send();

            $credits = $count * config('saas.credits.ussd_pull.amount');

            abort_unless(
                Auth::user()->canConsume($credits, 'credits'),
                403,
                __('messages.global.limit_exceeded')
            );

            return Redirect::back()->with('success', trans_choice('messages.ussd_pull.retry.success', $count));
        });
    }
}
