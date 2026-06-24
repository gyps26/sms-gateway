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

namespace App\Jobs;

use App\Enums\CampaignableStatus;
use App\Enums\MessageStatus;
use App\Models\Campaign;
use App\Models\MessageGateway;
use App\Models\SendingServer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

#[DeleteWhenMissingModels]
class SendCampaign implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private Campaign $campaign;

    private SendingServer $sendingServer;

    /**
     * Create a new job instance.
     */
    public function __construct(Campaign $campaign, SendingServer $sendingServer)
    {
        $this->campaign = $campaign;
        $this->sendingServer = $sendingServer;
    }

    /**
     * Execute the job.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(): void
    {
        $processed = 0;

        try {
            $messageGateway = MessageGateway::find($this->sendingServer->driver);

            $gateway = app()->make($messageGateway->class, ['sendingServer' => $this->sendingServer]);

            $this->campaign->sendingServers()->updateExistingPivot(
                $this->sendingServer->id,
                ['status' => CampaignableStatus::Queued]
            );

            $daysOfWeek = $this->campaign->days_of_week->map(fn ($day) => $day->value);
            $activeHours = explode('-', $this->campaign->active_hours);

            $query = $this->sendingServer->messages()
                                         ->whereStatus(MessageStatus::Pending)
                                         ->whereCampaignId($this->campaign->id);

            foreach ($query->lazyById(100) as $message) {
                // This will prevent N+1 query issue when accessing media.
                $message->setRelation('media', $this->campaign->getMedia('attachments'));

                $sendingServer = $this->campaign->sendingServers()->find($this->sendingServer->id);
                if ($sendingServer->pivot->status === CampaignableStatus::Cancelling) {
                    $this->campaign->sendingServers()->updateExistingPivot(
                        $this->sendingServer->id,
                        ['status' => CampaignableStatus::Cancelled]
                    );
                    return;
                }

                if ($this->sendingServer->quota->enabled && $this->sendingServer->quota->available <= $processed) {
                    $this->campaign->sendingServers()->updateExistingPivot(
                        $this->sendingServer->id,
                        [
                            'status' => CampaignableStatus::Stalled,
                            'resume_at' => $this->sendingServer->quota->reset_at
                        ]
                    );
                    return;
                }

                $now = now($this->campaign->timezone);
                $start = Carbon::parse("{$activeHours[0]}:00", $this->campaign->timezone);
                $end = Carbon::parse("{$activeHours[1]}:59", $this->campaign->timezone);

                if (! $now->between($start, $end) || $daysOfWeek->doesntContain($now->dayOfWeekIso)) {
                    $nextAvailableTime = $start->copy();

                    // Add day until we get to next available day of week.
                    if ($nextAvailableTime->isPast()) {
                        do {
                            $nextAvailableTime->addDay();
                        } while ($daysOfWeek->doesntContain($nextAvailableTime->dayOfWeekIso));
                    }

                    $this->campaign->sendingServers()->updateExistingPivot(
                        $this->sendingServer->id,
                        ['status' => CampaignableStatus::Stalled, 'resume_at' => $nextAvailableTime->utc()]
                    );
                    return;
                }

                if ($this->campaign->user->consume($message->credits)) {
                    if ($gateway->send($message)) {
                        $message->status = MessageStatus::Processed;
                    } else {
                        $message->status = MessageStatus::Failed;
                    }

                    $message->delivered_at = now();
                    $message->save();

                    $processed++;
                } else {
                    throw new AuthorizationException(__('messages.global.limit_exceeded'));
                }
            }

            $this->campaign->sendingServers()->updateExistingPivot(
                $this->sendingServer->id,
                ['status' => CampaignableStatus::Succeeded]
            );
        } catch (Throwable $e) {
            if (App::hasDebugModeEnabled()) {
                Log::error($e->getMessage(), ['exception' => $e]);
            }

            $this->campaign->sendingServers()->updateExistingPivot(
                $this->sendingServer->id,
                ['status' => CampaignableStatus::Failed]
            );
        } finally {
            if ($processed > 0 && $this->sendingServer->quota->enabled) {
                $this->sendingServer->quota->decrement('available', $processed);
            }
        }
    }
}
