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

namespace App;

use App\Enums\CampaignType;
use App\Helpers\Validators;
use App\Jobs\ProcessManualCampaign;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;
use Webklex\IMAP\Facades\Client;
use Webklex\PHPIMAP\Attachment;
use Webklex\PHPIMAP\Message as MailMessage;

class FetchEmails
{

    /**
     * Invoke the class instance.
     * @throws \Throwable
     */
    public function __invoke(): void
    {
        try {
            $client = Client::account('default');

            $client->connect();

            $folder = $client->getFolder('INBOX');

            $uidNext = data_get($folder->status(), 'uidnext');
            $uidValidity = data_get($folder->status(), 'uidvalidity');

            $lastUidValidity = Setting::retrieve('imap.last_uidvalidity');
            $lastUidNext = Setting::retrieve('imap.last_uidnext');

            if ($uidValidity === $lastUidValidity) {
                if ($uidNext === $lastUidNext) {
                    return;
                }

                $messages = $folder->messages()->getByUidGreater($lastUidNext);
            } else {
                $messages = $folder->messages()->unseen()->get();
            }

            if ($messages->count() > 0) {
                foreach ($messages as $message) {
                    $parsed = $this->parse($message);

                    if (is_null($parsed)) {
                        continue;
                    }

                    $user = User::whereEmail(data_get($message->from->get(), 'mail'))->first();

                    if (is_null($user) || ! Setting::retrieve('messaging.email_to_message', $user->id)) {
                        continue;
                    }

                    $sim = null;
                    $senderId = null;
                    $simsQuery = $user->activeSims();
                    $senderIdsQuery =
                        $user->senderIds()
                             ->whereHas('sendingServer', function ($query) use ($parsed) {
                                 $query->whereJsonContains('sending_servers.supported_types', $parsed['type'])
                                       ->whereEnabled(true);
                             });

                    if (is_null($parsed['senderType'])) {
                        $sim = $simsQuery->inRandomOrder()->first();
                        $senderId = $senderIdsQuery->inRandomOrder()->first();

                        if (isset($sim) && isset($senderId)) {
                            $sim = rand(0, 1) ? $sim : null;
                        }
                    } else if (strcasecmp($parsed['senderType'], 'SIM') === 0) {
                        $sim = $simsQuery->where('sims.id', $parsed['senderId'])->first();
                    } else if (strcasecmp($parsed['senderType'], 'SenderID') === 0) {
                        $senderId = $senderIdsQuery->where('sender_ids.id', $parsed['senderId'])->first();
                    }

                    if (is_null($sim) && is_null($senderId)) {
                        continue;
                    }

                    $data = [
                        'name' => 'Email to Message - ' . $message->from,
                        'type' => $parsed['type'],
                        'timezone' => 'UTC',
                        'days_of_week' => [1, 2, 3, 4, 5, 6, 7],
                        'active_hours' => '00:00-23:59',
                        'payload' => [
                            'mobile_numbers' => [$parsed['mobileNumber']],
                            'message' => $message->getTextBody()
                        ],
                        'options' => [
                            'delay' => 0,
                            'prioritize' => false
                        ]
                    ];

                    try {
                        DB::beginTransaction();

                        $credits = config('saas.credits.email_to_message.amount');

                        if (! $user->consume($credits)) {
                            DB::rollBack();
                            continue;
                        }

                        $campaign = $user->campaigns()->create($data);

                        if ($campaign->type !== CampaignType::Sms) {
                            $message->getAttachments()->each(function (Attachment $attachment) use ($campaign) {
                                $campaign->addMediaFromString($attachment->getContent())
                                         ->usingFileName($attachment->filename)
                                         ->toMediaCollection('attachments');
                            });
                        }

                        if ($sim) {
                            $campaign->devices()->attach($sim->device_id, [
                                'senders' => ['sims' => [$sim->id]]
                            ]);
                        } else if ($senderId) {
                            $campaign->sendingServers()->attach($senderId->sending_server_id, [
                                'senders' => ['sender_ids' => [$senderId->id]]
                            ]);
                        }

                        ProcessManualCampaign::dispatch($campaign);

                        DB::commit();
                    } catch (Throwable) {
                        DB::rollBack();
                    }

                    $message->setFlag('Seen');
                }
            }

            Setting::store('imap.last_uidvalidity', $uidValidity);
            Setting::store('imap.last_uidnext', $uidNext);
        } catch (Throwable $t) {
            Log::error("Error fetching emails: {$t->getMessage()}");
        }
    }

    private function parse(MailMessage $message): ?array
    {
        // Regex Explanation:
        // ^\s* - Matches any leading whitespace at the start of the string (optional).
        // (?:(SMS|MMS|WhatsApp)? - Captures the message type (SMS, MMS, or WhatsApp) if present (optional).
        // (?:\s+from\s+(SIM|SenderID)\s+(\d+))? - Matches "from" followed by either "SIM" or "SenderID" and a numeric ID (optional):
        //     \s+from\s+ - Matches "from" surrounded by one or more spaces.
        //     (SIM|SenderID) - Captures the sender type (SIM or SenderID).
        //     (\d+) - Captures the sender ID (one or more digits).
        // (?:\s+to)? - Matches "to" preceded by one or more spaces (optional).
        // \s+)? - Matches one or more spaces after the optional groups (optional).
        // (\+?\d{10,15}) - Captures the mobile number, which can optionally start with "+" and must have 10 to 15 digits (mandatory).
        // \s*$ - Matches any trailing whitespace at the end of the string (optional).
        // i - Makes the regex case-insensitive.
        $pattern = '/^\s*(?:(SMS|MMS|WhatsApp)?(?:\s+from\s+(SIM|SenderID)\s+(\d+))?(?:\s+to)?\s+)?(\+?\d{10,15})\s*$/i';

        if (! preg_match($pattern, $message->subject, $matches)) {
            return null;
        }

        $type = empty($matches[1]) ? ($message->getAttachments()->isEmpty() ? 'SMS' : 'MMS') : $matches[1];
        $senderType = empty($matches[2]) ? null : $matches[2];
        $senderId = empty($matches[3]) ? null : $matches[3];
        $mobileNumber = $matches[4];

        if (! Validators::isMobileNumber($mobileNumber)) {
            return null;
        }

        foreach (CampaignType::cases() as $campaignType) {
            if (strcasecmp($campaignType->value, $type) === 0) {
                $type = $campaignType;
                break;
            }
        }

        if (! $type instanceof CampaignType) {
            return null;
        }

        return [
            'type' => $type->value,
            'senderType' => $senderType,
            'senderId' => $senderId,
            'mobileNumber' => $mobileNumber,
        ];
    }
}

