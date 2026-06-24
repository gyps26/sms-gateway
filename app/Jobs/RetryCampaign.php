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

use App\Data\Filters\MessageFiltersData;
use App\Data\Filters\UssdPullFiltersData;
use App\DataTable;
use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Enums\MessageStatus;
use App\Enums\UssdPullStatus;
use App\Models\Campaign;
use App\Models\Campaignable;
use App\Models\Message;
use App\Models\UssdPull;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class RetryCampaign implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array<int, mixed>  $data
     */
    public function __construct(
        private readonly array $data,
        private readonly MessageFiltersData|UssdPullFiltersData $filters
    ) {}

    /**
     * Execute the job.
     *
     * @throws \Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            if ($this->filters instanceof MessageFiltersData) {
                $query = DataTable::make(Message::filter($this->filters))
                                  ->usingData($this->data)
                                  ->query()
                                  ->whereHas('campaign', fn($query) => $query->whereStatus('Completed'));

                $count = $query->update([
                    'status' => MessageStatus::Pending,
                    'sent_at' => now(),
                    'delivered_at' => null,
                    'response' => null,
                    'retries' => 0,
                ]);
            } else {
                $query = DataTable::make(UssdPull::filter($this->filters))
                                  ->usingData($this->data)
                                  ->search(fn($query, $search) => $query->search($search))
                                  ->query()
                                  ->whereHas('campaign', fn($query) => $query->where('status', CampaignStatus::Completed));

                $count = $query->update([
                    'status' => UssdPullStatus::Pending,
                    'sent_at' => now(),
                    'received_at' => null,
                    'response' => null,
                ]);
            }

            if ($count <= 0) {
                return;
            }

            $campaignIds = $query->distinct()->pluck('campaign_id')->unique();
            Campaignable::whereIn('campaign_id', $campaignIds)->update(['status' => CampaignableStatus::Pending]);
            Campaign::whereIn('id', $campaignIds)->get(['id'])->each->send();
        });
    }
}
