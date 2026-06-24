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

namespace App\Imports;

use App\Enums\CampaignStatus;
use App\Models\Blacklist;
use App\Models\Campaign;
use App\Models\Message;
use App\Monitor;
use App\Rules\MobileNumber;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Concerns\Importable;

class MessagesImport extends TrackedImport
{
    use Importable;

    private Campaign $campaign;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function monitor(): Monitor
    {
        return Monitor::for(Campaign::class, $this->campaign->id);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function processChunk(Collection $collection): void
    {
        $succeeded = $this->monitor()->processed - $this->monitor()->failures;

        $messages = [];
        foreach ($collection as $row) {
            $senderCount = $this->campaign->senders->count();
            $senderIndex = $senderCount > 0 ? $succeeded % $senderCount : 0;
            $sender = $this->campaign->senders->get($senderIndex);

            $message = $this->campaign->makeMessage(
                $row->get('mobile_number'),
                $sender,
                $row->except(['mobile_number'])->toArray()
            );

            $messages[] = $message;

            $succeeded++;
        }

        Message::insert($messages);
    }

    protected function rules(): array
    {
        return [
            'mobile_number' => ['required', new MobileNumber()]
        ];
    }

    protected function withValidator(Validator $validator, Collection $collection): void
    {
        $force = $this->campaign->options->get('force', false);

        if ($force) {
            return;
        }

        $mobileNumbers = $collection->pluck('mobile_number')
                                    ->unique()
                                    ->values();

        $blacklistedNumbers = Blacklist::whereIn('mobile_number', $mobileNumbers)
                                       ->whereUserId($this->campaign->user_id)
                                       ->pluck('mobile_number')
                                       ->toArray();

        $validator->after(function (Validator $validator) use ($blacklistedNumbers) {
            $mobileNumber = data_get($validator->attributes(), 'mobile_number');
            if (in_array($mobileNumber, $blacklistedNumbers)) {
                $validator->errors()->add(
                    'mobile_number',
                    __('validation.custom.blacklist')
                );
            }
        });
    }

    protected function beforeImport(): void
    {
        $this->campaign->update(['status' => CampaignStatus::Processing]);
    }

    protected function afterImport(): void
    {
        $this->campaign->update(['status' => CampaignStatus::Processed]);
    }
}
