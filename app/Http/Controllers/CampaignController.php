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

use App\Data\CreateMessagesData;
use App\Data\Filters\CampaignFiltersData;
use App\Data\Filters\MessageFiltersData;
use App\Data\Filters\UssdPullFiltersData;
use App\Data\PaginationData;
use App\Data\SendCampaignData;
use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\MessageStatus;
use App\Enums\UssdPullStatus;
use App\Exports\MessagesExport;
use App\Helpers\Environment;
use App\Helpers\Timezone;
use App\Http\Requests\SendMessagesRequest;
use App\Http\Requests\SendUssdPullsRequest;
use App\Http\Requests\UpdateCampaignRequest;
use App\Http\Resources\CampaignDeviceResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignSendingServerResource;
use App\Http\Resources\MessageResource;
use App\Http\Resources\UssdPullResource;
use App\Models\Campaign;
use App\Models\Message;
use App\Models\Setting;
use App\Models\User;
use App\Models\UssdPull;
use App\Monitor;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CampaignFiltersData $filters): Response|RedirectResponse
    {
        $query = Auth::user()->campaigns()->withCount(['devices', 'sendingServers'])->filter($filters);

        return DataTable::make($query)
                        ->search(fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
                        ->sort(['scheduled_at', 'status', 'recurring', 'type', 'timezone', 'created_at', 'ends_at'])
                        ->render('Campaigns/Index', fn($data) => [
                            'campaigns' => fn() => CampaignResource::collection($data()),
                            'params' => $filters->toArray()
                        ]);
    }

    /**
     * Show the form for creating a new message campaign.
     *
     * @throws \Exception
     */
    public function createMessages(CreateMessagesData $data): Response
    {
        $sims = Auth::user()
                    ->activeSims()
                    ->with(['device:id,model,name'])
                    ->get(['sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label', 'sims.device_id']);

        $contactLists = Auth::user()
                            ->contactLists()
                            ->withCount('contacts')
                            ->having('contacts_count', '>', 0)
                            ->get(['id', 'name']);

        $templates = Auth::user()
                         ->templates()
                         ->get(['id', 'name', 'content'])
                         ->keyBy('id');

        $senderIds = Auth::user()
                         ->activeSenderIds()
                         ->with(['sendingServer:id,name,supported_types'])
                         ->get(['sender_ids.id', 'sender_ids.value', 'sender_ids.sending_server_id'])
                         ->keyBy('id');

        $defaults = Setting::retrieve('default.send_messages', Auth::id(), config('default.send_messages'));

        return Inertia::render('Messages/Send', [
            'timezones' => Timezone::all(),
            'contactLists' => $contactLists,
            'sims' => $sims,
            'templates' => $templates,
            'senderIds' => $senderIds,
            'keywords' => Setting::retrieve('messaging.prompts.keywords', Auth::id(), []),
            'maxUploadSize' => Environment::getUploadMaxSize(),
            'params' => array_replace_recursive($defaults, array_filter($data->toArray(), fn($v) => isset($v)))
        ]);
    }

    /**
     * Show the form for creating a new USSD Pull campaign.
     * @throws \Exception
     */
    public function createUssdPulls(): Response
    {
        $sims = Auth::user()
                    ->activeSims()
                    ->with(['device:id,model,name'])
                    ->get(['sims.id', 'sims.number', 'sims.name', 'sims.carrier', 'sims.label', 'sims.device_id']);

        $defaults = Setting::retrieve('default.send_ussd_pulls', Auth::id(), config('default.send_ussd_pulls'));

        return Inertia::render('USSD/Send', [
            'timezones' => Timezone::all(),
            'sims' => $sims,
            'params' => $defaults
        ]);
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Throwable
     */
    public function sendMessages(SendMessagesRequest $request, #[CurrentUser] User $user): RedirectResponse
    {
        $data = SendCampaignData::from($request->validated());

        $user->createMessageCampaign($data);

        $newDefaults = $request->safe()->only([
            'timezone', 'recipients', 'type', 'active_hours', 'days_of_week', 'delivery_report', 'delay'
        ]);

        $defaults = Setting::whereUserId(Auth::id())
                           ->whereName('default.send_messages')
                           ->first();

        if ($defaults) {
            $defaults->update(['value' => $newDefaults + $defaults->value]);
        } else {
            Setting::create([
                'name' => 'default.send_messages',
                'value' => $newDefaults,
                'user_id' => Auth::id()
            ]);
        }

        return Redirect::back()->with('success', __('messages.campaign.created'));
    }

    /**
     * @throws \Throwable
     */
    public function sendUssdPulls(SendUssdPullsRequest $request, #[CurrentUser] User $user): RedirectResponse
    {
        $data = SendCampaignData::from($request->validated());

        $user->createUssdPullCampaign($data);

        Setting::store(
            'default.send_ussd_pulls',
            $request->safe()->only(['active_hours', 'days_of_week', 'timezone', 'delay']),
            Auth::id()
        );

        return Redirect::back()->with('success', __('messages.campaign.created'));
    }

    /**
     * Display the specified resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function show(Request $request, Campaign $campaign): Response|RedirectResponse
    {
        Gate::authorize('view', $campaign);

        $users = function () use ($campaign) {
            $users = Collection::wrap(
                Auth::user()->setRelations([
                    'sims' => $campaign->sims()->whereActive(true)->get()->keyBy('id'),
                    'senderIds' => $campaign->senderIds->keyBy('id')
                ])
            );

            return $users->keyBy('id');
        };

        $data = [
            'campaign' => fn() => $campaign->loadCount(['devices', 'sendingServers']),
            'campaignStatus' => fn() => Monitor::for(Campaign::class, $campaign->id),
            'users' => $users,
        ];

        $payload = $request->except('campaign', 'user', 'type');

        if ($campaign->type === CampaignType::UssdPull) {
            $filters = UssdPullFiltersData::validateAndCreate($payload);

            return DataTable::make($campaign->ussdPulls()->filter($filters))
                            ->search(fn($query, $search) => $query->search($search))
                            ->sort(fields: ['code', 'received_at', 'created_at'], default: 'received_at')
                            ->render('Campaigns/ShowUssdPulls', fn($ussdPulls) => $data + [
                                'ussdPulls' => fn() => UssdPullResource::collection($ussdPulls()),
                                'params' => $filters->toArray()
                            ]);
        } else {
            $filters = MessageFiltersData::validateAndCreate($payload);
            $pagination = PaginationData::validateAndCreate($payload);

            $messages = function () use ($campaign, $filters, $pagination) {
                $messages = $campaign->messages()
                                     ->with('media', 'campaign.media')
                                     ->filter($filters)
                                     ->orderBy('delivered_at', 'desc')
                                     ->paginate($pagination->perPage)
                                     ->withQueryString();

                abort_if($pagination->page > $messages->lastPage(), Redirect::to($messages->url($messages->lastPage())));

                Message::loadPhoneIds($messages->getCollection());

                return MessageResource::collection($messages);
            };

            return Inertia::render('Campaigns/ShowMessages', $data + [
                'messages' => $messages,
                'exportStatus' => fn() => Monitor::for(MessagesExport::class, Auth::id()),
                'params' => $filters->toArray() + $pagination->toArray()
            ]);
        }
    }

    public function delete(CampaignFiltersData $filters): RedirectResponse
    {
        $count = DataTable::make(Auth::user()->campaigns()->filter($filters))
                          ->search(fn($query, $search) => $query->where('name', 'like', "%{$search}%"))
                          ->query()
                          ->delete();

        if ($count > 0) {
            return Redirect::back()->with('success', trans_choice('messages.campaign.delete.success', $count));
        }

        return Redirect::back()->with('error', __('messages.campaign.delete.failed'));
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function dashboard(Campaign $campaign): Response
    {
        Gate::authorize('view', $campaign);

        if ($campaign->type === CampaignType::UssdPull) {
            $counts = UssdPull::whereCampaignId($campaign->id)
                              ->select(['status', DB::raw('count(*) as count')])
                              ->groupBy('status')
                              ->get()
                              ->mapWithKeys(fn($item) => [Str::slug($item->status->value, '_') => $item->count]);
        } else {
            $counts = Message::whereCampaignId($campaign->id)
                             ->select(['status', DB::raw('count(*) as count')])
                             ->groupBy('status')
                             ->get()
                             ->mapWithKeys(fn($item) => [Str::slug($item->status->value, '_') => $item->count]);
        }

        return Inertia::render('Campaigns/Dashboard', [
            'campaign' => fn() => $campaign->loadCount(['devices', 'sendingServers']),
            'counts' => fn() => $counts,
            'campaignStatus' => fn() => Monitor::for(Campaign::class, $campaign->id),
        ]);
    }

    /**
     * Download log file created by campaign job.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadImportLog(Campaign $campaign): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('update', $campaign);

        $monitor = Monitor::for(Campaign::class, $campaign->id);
        if ($monitor->logfile && Storage::exists($monitor->logfile)) {
            $filename = ($campaign->name ?? 'campaign-' . $campaign->id) . '-import.log';
            return response()->download(Storage::path($monitor->logfile), $filename);
        }
        return Redirect::back();
    }

    /**
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancel(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('cancel', $campaign);

        foreach ($campaign->campaignables as $campaignable) {
            $status = match ($campaignable->status) {
                CampaignableStatus::Stalled, CampaignableStatus::Pending => CampaignableStatus::Cancelled,
                default => CampaignableStatus::Cancelling
            };

            $campaignable->updateQuietly([
                'status' => $status,
                'resume_at' => null
            ]);
        }

        $completed = $campaign->campaignables->every(function ($campaignable) {
            return in_array($campaignable->status, [CampaignableStatus::Cancelled, CampaignableStatus::Succeeded, CampaignableStatus::Failed]);
        });

        if ($completed) {
            $campaign->update(['status' => CampaignStatus::Completed]);
        }

        return Redirect::back()->with('success', __('messages.campaign.cancelling'));
    }

    /**
     * @throws \Throwable
     */
    public function retry(Campaign $campaign): RedirectResponse
    {
        Gate::authorize('retry', $campaign);

        $campaign->campaignables()
                 ->whereIn('status', [CampaignableStatus::Failed, CampaignableStatus::Cancelled])
                 ->update([
                     'status' => CampaignableStatus::Pending,
                     'resume_at' => null
                 ]);

        $campaign->send();

        return Redirect::back()->with('success', __('messages.campaign.retried'));
    }

    /**
     * Overview of devices associated with resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function devices(Campaign $campaign): Response|RedirectResponse
    {
        Gate::authorize('view', $campaign);

        $closure = fn($query, $status) => $query->where('campaign_id', $campaign->id)->where('status', $status);

        if ($campaign->type === CampaignType::UssdPull) {
            $counts = [
                'ussdPulls as pending_count' => fn($query) => $closure($query, UssdPullStatus::Pending),
                'ussdPulls as queued_count' => fn($query) => $closure($query, UssdPullStatus::Queued),
                'ussdPulls as completed_count' => fn($query) => $closure($query, UssdPullStatus::Completed),
                'ussdPulls as failed_count' => fn($query) => $closure($query, UssdPullStatus::Failed),
            ];
        } else {
            $counts = [
                'messages as pending_count' => fn($query) => $closure($query, MessageStatus::Pending),
                'messages as queued_count' => fn($query) => $closure($query, MessageStatus::Queued),
                'messages as processed_count' => fn($query) => $closure($query, MessageStatus::Processed),
                'messages as sent_count' => fn($query) => $closure($query, MessageStatus::Sent),
                'messages as delivered_count' => fn($query) => $closure($query, MessageStatus::Delivered),
                'messages as failed_count' => fn($query) => $closure($query, MessageStatus::Failed),
            ];
        }

        $query = $campaign->devices()->select(['devices.id', 'devices.model', 'devices.name'])->withCount($counts);

        return DataTable::make($query)
                        ->search(function ($query, $search) {
                            $query->where('devices.name', 'like', "%{$search}%")
                                  ->orWhere('devices.model', 'like', "%{$search}%")
                                  ->orWhere('campaignables.status', $search);
                        })
                        ->sort([
                            'model',
                            'name',
                            'status',
                            'resume_at',
                            'pending_count',
                            'queued_count',
                            'processed_count',
                            'sent_count',
                            'delivered_count',
                            'failed_count'
                        ])
                        ->render('Campaigns/Devices', fn($data) => [
                            'campaign' => fn() => $campaign->loadCount(['devices', 'sendingServers']),
                            'devices' => fn() => CampaignDeviceResource::collection($data()),
                            'campaignStatus' => fn() => Monitor::for(Campaign::class, $campaign->id),
                        ]);
    }

    /**
     * Overview of sending servers associated with resource.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function sendingServers(Campaign $campaign): Response|RedirectResponse
    {
        Gate::authorize('view', $campaign);

        $closure = fn($query, $status) => $query->where('campaign_id', $campaign->id)->where('status', $status);

        $counts = [
            'messages as pending_count' => fn($query) => $closure($query, MessageStatus::Pending),
            'messages as queued_count' => fn($query) => $closure($query, MessageStatus::Queued),
            'messages as processed_count' => fn($query) => $closure($query, MessageStatus::Processed),
            'messages as sent_count' => fn($query) => $closure($query, MessageStatus::Sent),
            'messages as delivered_count' => fn($query) => $closure($query, MessageStatus::Delivered),
            'messages as failed_count' => fn($query) => $closure($query, MessageStatus::Failed),
        ];

        $query = $campaign->sendingServers()
                          ->select(['sending_servers.id', 'sending_servers.name'])
                          ->withCount($counts);

        return DataTable::make($query)
                        ->search(function ($query, $search) {
                            $query->where('sending_servers.name', 'like', "%{$search}%")
                                  ->orWhere('campaign_sending_server.status', $search);
                        })->sort([
                            'name',
                            'status',
                            'resume_at',
                            'pending_count',
                            'queued_count',
                            'processed_count',
                            'sent_count',
                            'delivered_count',
                            'failed_count'
                        ])->render('Campaigns/SendingServers', fn($data) => [
                            'campaign' => fn() => $campaign->loadCount(['devices', 'sendingServers']),
                            'sendingServers' => fn() => CampaignSendingServerResource::collection($data()),
                            'campaignStatus' => fn() => Monitor::for(Campaign::class, $campaign->id)
                        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCampaignRequest  $request
     * @param  \App\Models\Campaign  $campaign
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateCampaignRequest $request, Campaign $campaign): RedirectResponse
    {
        $campaign->update([
            'name' => $request->input('name')
        ]);

        return Redirect::back()->with('success', __('messages.campaign.updated'));
    }
}
