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

use App\Contracts\Messageable;
use App\Data\Filters\CampaignFiltersData;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use App\Enums\DayOfWeek;
use App\Enums\FrequencyUnit;
use App\Enums\SubscriptionStatus;
use App\Helpers\Common;
use App\Imports\MessagesImport;
use App\Jobs\ProcessContactListsCampaign;
use App\Jobs\ProcessManualCampaign;
use App\Jobs\ProcessUssdCampaign;
use App\Models\Scopes\DateRange;
use App\Observers\CampaignObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\SchemalessAttributes\SchemalessAttributesTrait;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\JsonKey;

#[ObservedBy([CampaignObserver::class])]
/**
 * @property int $id
 * @property string|null $name
 * @property \Illuminate\Support\Carbon|null $scheduled_at
 * @property string $timezone
 * @property bool $recurring
 * @property \Illuminate\Support\Carbon|null $repeat_at
 * @property int|null $frequency
 * @property FrequencyUnit|null $frequency_unit
 * @property \Illuminate\Support\Carbon|null $ends_at
 * @property string $active_hours
 * @property \Illuminate\Support\Collection<int, DayOfWeek> $days_of_week
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $payload
 * @property \Spatie\SchemalessAttributes\SchemalessAttributes|null $options
 * @property CampaignType $type
 * @property CampaignStatus $status
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaignable> $campaignables
 * @property-read int|null $campaignables_count
 * @property-read \App\Models\Campaignable|\App\Models\CampaignContactList|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContactList> $contactLists
 * @property-read int|null $contact_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read mixed $label
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read mixed $senders
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SendingServer> $sendingServers
 * @property-read int|null $sending_servers_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UssdPull> $ussdPulls
 * @property-read int|null $ussd_pulls_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sim[] $sims
 * @property-read int|null $sims_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SenderId[] $senderIds
 * @property-read int|null $sender_ids_count
 * @method static \Database\Factories\CampaignFactory factory($count = null, $state = [])
 * @method static Builder<static>|Campaign filter(\App\Data\Filters\CampaignFiltersData $filters)
 * @method static Builder<static>|Campaign newModelQuery()
 * @method static Builder<static>|Campaign newQuery()
 * @method static Builder<static>|Campaign query()
 * @method static Builder<static>|Campaign whereActiveHours($value)
 * @method static Builder<static>|Campaign whereCreatedAt($value)
 * @method static Builder<static>|Campaign whereDaysOfWeek($value)
 * @method static Builder<static>|Campaign whereEndsAt($value)
 * @method static Builder<static>|Campaign whereFrequency($value)
 * @method static Builder<static>|Campaign whereFrequencyUnit($value)
 * @method static Builder<static>|Campaign whereId($value)
 * @method static Builder<static>|Campaign whereName($value)
 * @method static Builder<static>|Campaign whereOptions($value)
 * @method static Builder<static>|Campaign wherePayload($value)
 * @method static Builder<static>|Campaign whereRecurring($value)
 * @method static Builder<static>|Campaign whereRepeatAt($value)
 * @method static Builder<static>|Campaign whereScheduledAt($value)
 * @method static Builder<static>|Campaign whereStatus($value)
 * @method static Builder<static>|Campaign whereTimezone($value)
 * @method static Builder<static>|Campaign whereType($value)
 * @method static Builder<static>|Campaign whereUpdatedAt($value)
 * @method static Builder<static>|Campaign whereUserId($value)
 * @method static Builder<static>|Campaign withOptionsAttributes()
 * @method static Builder<static>|Campaign withPayloadAttributes()
 * @mixin \Eloquent
 */
class Campaign extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SchemalessAttributesTrait;
    use HasRelationships;
    use HasJsonRelationships;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'repeat_at' => 'datetime',
            'frequency' => 'integer',
            'frequency_unit' => FrequencyUnit::class,
            'ends_at' => 'datetime',
            'recurring' => 'boolean',
            'days_of_week' => AsEnumCollection::class . ':' . DayOfWeek::class,
            'status' => CampaignStatus::class,
            'type' => CampaignType::class,
            'user_id' => 'integer',
        ];
    }

    protected $schemalessAttributes = [
        'payload',
        'options',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['label'];

    public function scopeWithPayloadAttributes(): Builder
    {
        return $this->payload->modelScope();
    }

    public function scopeWithOptionsAttributes(): Builder
    {
        return $this->options->modelScope();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function devices(): MorphToMany
    {
        return $this->morphedByMany(Device::class, 'campaignable')
                    ->using(Campaignable::class)
                    ->withPivot(['senders', 'status', 'resume_at']);
    }

    public function sendingServers(): MorphToMany
    {
        return $this->morphedByMany(SendingServer::class, 'campaignable')
                    ->using(Campaignable::class)
                    ->withPivot(['senders', 'status', 'resume_at']);
    }

    public function campaignables(): HasMany
    {
        return $this->hasMany(Campaignable::class);
    }

    public function contactLists(): BelongsToMany
    {
        return $this->belongsToMany(ContactList::class)->using(CampaignContactList::class)->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function ussdPulls(): HasMany
    {
        return $this->hasMany(UssdPull::class);
    }

    protected function label(): Attribute
    {
        return Attribute::make(fn () => $this->name ?? "Campaign #{$this->id}");
    }

    protected function senders(): Attribute
    {
        return Attribute::make(fn () => $this->sims->merge($this->senderIds));
    }

    public function sims(): HasManyDeep
    {
        return $this->hasManyThroughJson(
            Sim::class, Campaignable::class, 'campaign_id', 'id', null, new JsonKey('senders->sims')
        );
    }

    public function senderIds(): HasManyDeep
    {
        return $this->hasManyThroughJson(
            SenderId::class, Campaignable::class, 'campaign_id', 'id', null, new JsonKey('senders->sender_ids')
        );
    }

    public function scopeFilter(Builder $query, CampaignFiltersData $filters)
    {
        return $query->when($filters->status, fn($query, $status) => $query->where('status', $status))
                     ->when($filters->type, fn($query, $type) => $query->where('type', $type))
                     ->when($filters->recurring, fn($query, $recurring) => $query->where('recurring', $recurring))
                     ->tap(new DateRange($filters->after, $filters->before, 'created_at'));
    }

    public function makeMessage(string $to, Messageable $sender, array $additional = []): array
    {
        $content = transform($this->payload->message, function ($message) use ($additional) {
            $message = Common::spintax($message, $additional);
            $subscription = $this->user->currentSubscription;
            if ($subscription?->plan_id === config('saas.trial.plan_id') && config('saas.trial.footer')) {
                $message .= "\n\n" . config('saas.trial.footer');
            }
            return $message;
        });

        return [
            'from' => $sender->fromAddress(),
            'to' => $to,
            'content' => $content,
            'type' => $this->type->value,
            'sent_at' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
            'user_id' => $this->user_id,
            'campaign_id' => $this->id,
            'messageable_id' => $sender->id,
            'messageable_type' => $sender->getMorphClass(),
        ];
    }

    public function makeUssdPull(string $code, Sim $sim): array
    {
        return [
            'from' => $sim->fromAddress(),
            'code' => $code,
            'user_id' => $this->user_id,
            'sim_id' => $sim->id,
            'campaign_id' => $this->id,
        ];
    }

    public function send(): void
    {
        $this->update(['status' => CampaignStatus::Processed]);

        $this->devices->each(function (Device $device) {
            $device->send($this);
        });

        $this->sendingServers->each(function (SendingServer $sendingServer) {
            $sendingServer->send($this);
        });
    }

    /**
     * @param  int[]  $sims
     */
    public function attachSims(array $sims): void
    {
        Sim::findMany($sims, ['id', 'device_id'])
           ->groupBy('device_id')
           ->each(function (Collection $sims, int $device_id) {
               $this->devices()->attach($device_id, ['senders' => ['sims' => $sims->pluck('id')]]);
           });
    }

    /**
     * @param  int[]  $senderIds
     */
    public function attachSenderIds(array $senderIds): void
    {
        SenderId::findMany($senderIds, ['id', 'sending_server_id'])
                ->groupBy('sending_server_id')
                ->each(function (Collection $senderIds, int $sending_server_id) {
                    $this->sendingServers()->attach($sending_server_id, ['senders' => ['sender_ids' => $senderIds->pluck('id')]]);
                });
    }

    public function repeat(): void
    {
        $campaign = $this->replicate([
            'scheduled_at',
            'repeat_at',
            'ends_at',
            'recurring',
            'status',
            'frequency',
            'frequency_unit'
        ]);

        $campaign->save();

        $this->getMedia('attachments')->each(function (Media $media) use ($campaign) {
            $media->copy($campaign, 'attachments');
        });

        if ($this->type !== CampaignType::UssdPull) {
            $campaign->contactLists()->sync($this->contactLists()->pluck('contact_lists.id'));

            $this->sendingServers->each(function (SendingServer $sendingServer) use ($campaign) {
                $campaign->sendingServers()->attach($sendingServer->id, ['senders' => $sendingServer->pivot->senders]);
            });
        }

        $this->devices->each(function (Device $device) use ($campaign) {
            $campaign->devices()->attach($device->id, ['senders' => $device->pivot->senders]);
        });

        $repeatAt = $this->repeat_at->add($this->frequency, $this->frequency_unit->value);
        $this->update(['repeat_at' => $repeatAt <= $this->ends_at ? $repeatAt: null]);

        if ($this->payload->mobile_numbers) {
            ProcessManualCampaign::dispatch($campaign);
        } elseif ($this->contactLists()->exists()) {
            ProcessContactListsCampaign::dispatch($campaign);
        } elseif ($this->getFirstMedia('spreadsheet')) {
            (new MessagesImport($campaign))->queue($this->getFirstMedia('spreadsheet')->getPath());
        } elseif ($this->payload->ussd_codes) {
            ProcessUssdCampaign::dispatch($campaign);
        }
    }
}
