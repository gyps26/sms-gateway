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

use App\Data\Filters\MessageFiltersData;
use App\Data\PaginationData;
use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\MessageStatus;
use App\Exports\MessagesExport;
use App\Http\Resources\MessageResource;
use App\Jobs\RetryCampaign;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\User;
use App\Monitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(MessageFiltersData $filters, PaginationData $pagination): Response|RedirectResponse
    {
        $users = function () {
            $relations = [
                'sims' => function ($query) {
                    $query->select('sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label')
                          ->whereActive(true);
                },
                'senderIds:sender_ids.id,sender_ids.value'
            ];

            if (Auth::user()->is_admin) {
                $users = User::with($relations)->get();
            } else {
                $users = Collection::wrap(
                    Auth::user()->load($relations)
                );
            }

            $users->map(function ($user) {
                $user->setRelation('sims', $user->sims->keyBy('id'));
                $user->setRelation('senderIds', $user->senderIds->keyBy('id'));
            });

            return $users->keyBy('id');
        };

        $messages = function () use ($filters, $pagination) {
            $messages = Message::with(['media', 'campaign.media'])
                               ->filter($filters)
                               ->latest('delivered_at')
                               ->latest('sent_at')
                               ->paginate($pagination->perPage)
                               ->withQueryString();

            abort_if($pagination->page > $messages->lastPage(), Redirect::to($messages->url($messages->lastPage())));

            Message::loadPhoneIds($messages->getCollection());

            return MessageResource::collection($messages);
        };

        $exportStatus = fn() => Monitor::for(MessagesExport::class, Auth::id());

        return Inertia::render('Messages/Index', [
            'messages' => $messages,
            'users' => $users,
            'exportStatus' => $exportStatus,
            'params' => $filters->toArray() + $pagination->toArray()
        ]);
    }

    /**
     * Remove the specified resources from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException|\Illuminate\Validation\ValidationException
     */
    public function delete(MessageFiltersData $filters): RedirectResponse
    {
        $count = DataTable::make(Message::filter($filters))->query()->delete();

        if ($count > 0) {
            return Redirect::back()->with('success', trans_choice('messages.message.delete.success', $count));
        }

        return Redirect::back()->with('error', __('messages.message.delete.failed'));
    }

    /**
     * Export resources to excel.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function dispatchExport(MessageFiltersData $filters): RedirectResponse
    {
        Gate::authorize('export', Message::class);

        $id = Auth::id();

        (new MessagesExport())
            ->forFilters($filters)
            ->queue(Message::exportFile())
            ->chain([
                function () use ($id) {
                    $monitor = Monitor::for(MessagesExport::class, $id);
                    $monitor->status = 'Completed';
                }
            ]);

        return Redirect::back()->with('success', 'Excel file export queued successfully.');
    }

    /**
     * Download exported file.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadExport(): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('export', Message::class);

        if (Storage::exists(Message::exportFile())) {
            return response()->download(Storage::path(Message::exportFile()), 'messages');
        }

        return Redirect::back()->with('error', __('messages.global.error'));
    }

    /**
     * @throws \Throwable
     */
    public function retry(MessageFiltersData $filters): RedirectResponse
    {
        if (is_null($filters->campaign)) {
            RetryCampaign::dispatch(request()->all(), $filters);
            return Redirect::back()->with('success', __('messages.message.retry.queued'));
        }

        return DB::transaction(function () use ($filters) {
            $count = DataTable::make(Message::filter($filters))
                              ->query()
                              ->whereHas('campaign', fn($query) => $query->whereStatus('Completed'))
                              ->update([
                                  'status' => MessageStatus::Pending,
                                  'sent_at' => now(),
                                  'delivered_at' => null,
                                  'response' => null,
                                  'retries' => 0
                              ]);

            if ($count <= 0) {
                return Redirect::back()->with('error', __('messages.message.retry.failed'));
            }

            $campaign = Campaign::findOrFail($filters->campaign);

            $campaign->campaignables()->update(['status' => CampaignableStatus::Pending]);

            $campaign->send();

            abort_unless(
                Auth::user()->canConsume($count, 'credits'),
                403,
                __('messages.global.limit_exceeded')
            );

            return Redirect::back()->with('success', trans_choice('messages.message.retry.success', $count));
        });
    }
}
