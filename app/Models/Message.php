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

namespace App\Models;

use App\Data\Filters\MessageFiltersData;
use App\Enums\MessageStatus;
use App\Enums\MessageType;
use App\Facades\Sms;
use App\Models\Scopes\DateRange;
use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\SchemalessAttributes\Casts\SchemalessAttributes;

#[ObservedBy(MessageObserver::class)]
/**
 * @property int $id
 * @property string|null $from
 * @property string|null $to
 * @property string|null $content
 * @property MessageType $type
 * @property MessageStatus $status
 * @property int $retries
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $response
 * @property int|null $campaign_id
 * @property int $user_id
 * @property string $messageable_type
 * @property int $messageable_id
 * @property \Illuminate\Support\Carbon $sent_at
 * @property \Illuminate\Support\Carbon|null $delivered_at
 * @property-read \App\Models\Campaign|null $campaign
 * @property-read mixed $credits
 * @property-read mixed $error
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, \Spatie\MediaLibrary\MediaCollections\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read Model|\Eloquent $messageable
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\MessageFactory factory($count = null, $state = [])
 * @method static Builder<static>|Message filter(\App\Data\Filters\MessageFiltersData $filters)
 * @method static Builder<static>|Message newModelQuery()
 * @method static Builder<static>|Message newQuery()
 * @method static Builder<static>|Message query()
 * @method static Builder<static>|Message whereCampaignId($value)
 * @method static Builder<static>|Message whereContent($value)
 * @method static Builder<static>|Message whereDeliveredAt($value)
 * @method static Builder<static>|Message whereFrom($value)
 * @method static Builder<static>|Message whereId($value)
 * @method static Builder<static>|Message whereMessageableId($value)
 * @method static Builder<static>|Message whereMessageableType($value)
 * @method static Builder<static>|Message whereResponse($value)
 * @method static Builder<static>|Message whereRetries($value)
 * @method static Builder<static>|Message whereSentAt($value)
 * @method static Builder<static>|Message whereStatus($value)
 * @method static Builder<static>|Message whereTo($value)
 * @method static Builder<static>|Message whereType($value)
 * @method static Builder<static>|Message whereUserId($value)
 * @mixin \Eloquent
 */
class Message extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'retries' => 'integer',
            'campaign_id' => 'integer',
            'user_id' => 'integer',
            'messageable_id' => 'integer',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'attachments' => 'array',
            'response' => SchemalessAttributes::class,
            'status' => MessageStatus::class,
            'type' => MessageType::class
        ];
    }

    public static function exportFile(): string
    {
        return 'public/' . hash('sha256', 'Messages-' . Auth::id()) . '.xlsx';
    }

    public function messageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function credits(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->type === MessageType::Sms) {
                    if (config('saas.credits.sms.per_part')) {
                        return Sms::getPartsCount($this->content) * config('saas.credits.sms.amount');
                    }

                    return config('saas.credits.sms.amount');
                } else {
                    return config('saas.credits.mms.amount');
                }
            },
        );
    }

    public function error(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->response->message) {
                    return $this->response->message;
                }

                if (is_null($this->response->result_code)) {
                    return null;
                }

                $error = null;
                if ($this->type === MessageType::Sms) {
                    $error = match ($this->response->result_code) {
                        -1 => null,
                        0 => 'DELIVERY_FAILURE',
                        1 => 'GENERIC_FAILURE',
                        2 => 'RADIO_OFF',
                        3 => 'NULL_PDU',
                        4 => 'NO_SERVICE',
                        5 => 'LIMIT_EXCEEDED',
                        6 => 'ERROR_FDN_CHECK_FAILURE',
                        7 => 'SHORT_CODE_NOT_ALLOWED',
                        8 => 'SHORT_CODE_NEVER_ALLOWED',
                        9 => 'RADIO_NOT_AVAILABLE',
                        10 => 'NETWORK_REJECT',
                        11 => 'INVALID_ARGUMENTS',
                        12 => 'INVALID_STATE',
                        13 => 'NO_MEMORY',
                        14 => 'INVALID_SMS_FORMAT',
                        15 => 'SYSTEM_ERROR',
                        16 => 'MODEM_ERROR',
                        17 => 'NETWORK_ERROR',
                        18 => 'ENCODING_ERROR',
                        19 => 'INVALID_SMSC_ADDRESS',
                        20 => 'OPERATION_NOT_ALLOWED',
                        21 => 'INTERNAL_ERROR',
                        22 => 'NO_RESOURCES',
                        23 => 'CANCELLED',
                        24 => 'REQUEST_NOT_SUPPORTED',
                        25 => 'NO_BLUETOOTH_SERVICE',
                        26 => 'INVALID_BLUETOOTH_ADDRESS',
                        27 => 'BLUETOOTH_DISCONNECTED',
                        28 => 'UNEXPECTED_EVENT_STOP_SENDING',
                        29 => 'SMS_BLOCKED_DURING_EMERGENCY',
                        30 => 'SMS_SEND_RETRY_FAILED',
                        31 => 'REMOTE_EXCEPTION',
                        32 => 'NO_DEFAULT_SMS_APP',
                        100 => 'RIL_RADIO_NOT_AVAILABLE',
                        101 => 'RIL_SMS_SEND_FAIL_RETRY',
                        102 => 'RIL_NETWORK_REJECT',
                        103 => 'RIL_INVALID_STATE',
                        104 => 'RIL_INVALID_ARGUMENTS',
                        105 => 'RIL_NO_MEMORY',
                        106 => 'RIL_REQUEST_RATE_LIMITED',
                        107 => 'RIL_INVALID_SMS_FORMAT',
                        108 => 'RIL_SYSTEM_ERR',
                        109 => 'RIL_ENCODING_ERR',
                        110 => 'RIL_INVALID_SMSC_ADDRESS',
                        111 => 'RIL_MODEM_ERR',
                        112 => 'RIL_NETWORK_ERR',
                        113 => 'RIL_INTERNAL_ERR',
                        114 => 'RIL_REQUEST_NOT_SUPPORTED',
                        115 => 'RIL_INVALID_MODEM_STATE',
                        116 => 'RIL_NETWORK_NOT_READY',
                        117 => 'RIL_OPERATION_NOT_ALLOWED',
                        118 => 'RIL_NO_RESOURCES',
                        119 => 'RIL_CANCELLED',
                        120 => 'RIL_SIM_ABSENT',
                        121 => 'RIL_SIMULTANEOUS_SMS_AND_CALL_NOT_ALLOWED',
                        122 => 'RIL_ACCESS_BARRED',
                        123 => 'RIL_BLOCKED_DUE_TO_CALL',
                        default => "UNKNOWN_ERROR_{$this->response->result_code}",
                    };
                } else if ($this->type === MessageType::Mms) {
                    $error = match ($this->response->result_code) {
                        -1 => null,
                        1 => 'MMS_ERROR_UNSPECIFIED',
                        2 => 'MMS_ERROR_INVALID_APN',
                        3 => 'MMS_ERROR_UNABLE_CONNECT_MMS',
                        4 => 'MMS_ERROR_HTTP_FAILURE',
                        5 => 'MMS_ERROR_IO_ERROR',
                        6 => 'MMS_ERROR_RETRY',
                        7 => 'MMS_ERROR_CONFIGURATION_ERROR',
                        8 => 'MMS_ERROR_NO_DATA_NETWORK',
                        9 => 'MMS_ERROR_INVALID_SUBSCRIPTION_ID',
                        10 => 'MMS_ERROR_INACTIVE_SUBSCRIPTION',
                        11 => 'MMS_ERROR_DATA_DISABLED',
                        12 => 'MMS_ERROR_MMS_DISABLED_BY_CARRIER',
                        13 => 'MMS_ERROR_TOO_LARGE_FOR_TRANSPORT',
                        default => "UNKNOWN_ERROR_{$this->response->result_code}",
                    };
                }

                $code = transform($this->response->error_code, function ($value) {
                    if ($value !== -1 && ($this->response->result_code === 1 || $this->response->result_code >= 100)) {
                        return $value;
                    }
                    return null;
                });

                if (is_null($code)) {
                    return $error;
                } else {
                    return "$error ($code)";
                }
            },
        );
    }

    public function scopeFilter(Builder $query, MessageFiltersData $filters): void
    {
        $query->whereNotNull(['messageable_id', 'messageable_type'])
              ->when($filters->user, fn($query, $user) => $query->where('user_id', $user))
              ->when($filters->campaign, fn($query, $campaign) => $query->where('campaign_id', $campaign))
              ->when($filters->sim, function ($query, $sim) {
                  return $query->where('messageable_id', $sim)->whereMorphedTo('messageable', Sim::class);
              })
              ->when($filters->senderId, function ($query, $senderId) {
                  return $query->where('messageable_id', $senderId)->whereMorphedTo('messageable', SenderId::class);
              })
              ->when($filters->mobileNumber, function ($query, $mobileNumber) {
                  return $query->where(fn($query) => $query->where('to', 'LIKE', "%{$mobileNumber}%")
                                                           ->orWhere('from', 'LIKE', "%{$mobileNumber}%"));
              })
              ->when($filters->message, fn($query, $message) => $query->where('content', 'LIKE', "%{$message}%"))
              ->when($filters->type, fn($query, $type) => $query->where('type', $type))
              ->when($filters->statuses, fn($query, $statuses) => $query->whereIn('status', $statuses))
              ->tap(new DateRange($filters->after, $filters->before));
    }

    public static function loadPhoneIds(Collection $messages): void
    {
        $getPhoneNumber = function (Message $message) {
            if ($message->status === MessageStatus::Received) {
                return $message->from;
            }

            return $message->to;
        };

        $phoneNumbers = $messages->map($getPhoneNumber)->unique();

        $ids = Contact::identify($phoneNumbers);

        if ($ids->isEmpty()) {
            return;
        }

        foreach ($messages as $message) {
            $message->phone_id = $ids->get($getPhoneNumber($message));
        }
    }
}
