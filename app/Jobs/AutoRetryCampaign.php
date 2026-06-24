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
use App\Models\SenderId;
use App\Models\Setting;
use App\Models\Sim;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\DeleteWhenMissingModels;
use Illuminate\Support\Facades\DB;

#[DeleteWhenMissingModels]
class AutoRetryCampaign implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private readonly Campaign $campaign)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $config = Setting::retrieve('messaging.auto_retry', $this->campaign->user_id, config('messaging.auto_retry'));

        // Build a base query for failed messages that haven't reached the max retry limit
        $baseQuery = $this->campaign->messages()
                                    ->whereStatus(MessageStatus::Failed)
                                    ->where('retries', '<', $config['max_attempts']);

        $count = 0;
        // Proceed if there are failed messages to retry
        if ($baseQuery->clone()->exists()) {
            // If change_after is set and there are multiple campaignables, consider changing the sender
            if (isset($config['change_after']) && $this->campaign->campaignables()->count() > 1) {
                // Query messages that have reached the change_after retry count
                $query = $baseQuery->clone()->where('retries', $config['change_after']);

                // If such messages exist, select the best performing sender and update the message sender
                if ($query->clone()->exists()) {
                    // Determine the status to use for delivery percentage calculation
                    $status = $this->campaign->options->delivery_report ? MessageStatus::Delivered : MessageStatus::Sent;

                    // Find the sender (SIM or Sender ID) with the highest delivery percentage other than the current campaignable's senders
                    $messages = $this->campaign->messages()
                                               ->select('messageable_id', 'messageable_type', DB::raw('SUM(status = "' . $status->value . '") * 100 / COUNT(*) as percentage'))
                                               ->groupBy('messageable_id', 'messageable_type')
                                               ->orderBy('percentage', 'desc')
                                               ->get();

                    foreach ($this->campaign->devices as $device) {
                        $sims = data_get($device->pivot->senders, 'sims', []);

                        $message = $messages->first(fn($msg) =>
                            $msg->messageable_type !== Relation::getMorphAlias(Sim::class) || in_array($msg->messageable_id, $sims) === false
                        );

                        $count += $query->clone()
                                        ->whereIn('messageable_id', $sims)
                                        ->whereMorphedTo('messageable', Sim::class)
                                        ->increment('retries', 1, [
                                            'status' => MessageStatus::Pending,
                                            'response' => null,
                                            'messageable_id' => $message->messageable_id,
                                            'messageable_type' => $message->messageable_type
                                        ]);
                    }

                    foreach ($this->campaign->sendingServers as $sendingServer) {
                        $senderIds = data_get($sendingServer->pivot->senders, 'sender_ids', []);

                        $message = $messages->first(fn($msg) =>
                            $msg->messageable_type !== Relation::getMorphAlias(SenderId::class) || in_array($msg->messageable_id, $senderIds) === false
                        );

                        $count += $query->clone()
                                        ->whereIn('messageable_id', $senderIds)
                                        ->whereMorphedTo('messageable', SenderId::class)
                                        ->increment('retries', 1, [
                                            'status' => MessageStatus::Pending,
                                            'response' => null,
                                            'messageable_id' => $message->messageable_id,
                                            'messageable_type' => $message->messageable_type
                                        ]);
                    }
                }

                // Increment retries for other messages that haven't reached change_after
                $count += $baseQuery->clone()
                                    ->whereNot('retries', $config['change_after'])
                                    ->increment('retries', 1, ['status' => MessageStatus::Pending, 'response' => null]);
            } else {
                // If change_after is not set or only one campaignable, retry all failed messages
                $count = $baseQuery->clone()
                                   ->increment('retries', 1, ['status' => MessageStatus::Pending, 'response' => null]);
            }

            if ($count <= 0) {
                return;
            }

            // If any messages were retried, trigger sending for this campaignable
            $this->campaign->campaignables()->update([
                'status' => CampaignableStatus::Pending,
                'resume_at' => null
            ]);

            $this->campaign->send();
        }
    }
}
