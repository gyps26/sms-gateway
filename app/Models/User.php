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

use App\Contracts\JwtSubject;
use App\Data\SendCampaignData;
use App\Enums\CampaignType;
use App\Enums\WebhookEvent;
use App\Helpers\Common;
use App\Imports\MessagesImport;
use App\Jobs\ProcessContactListsCampaign;
use App\Jobs\ProcessManualCampaign;
use App\Jobs\ProcessUssdCampaign;
use App\Models\Scopes\CurrentSubscription;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Exception\MissingDependencyException;
use Intervention\Image\Exception\NotSupportedException;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use LasseRafn\InitialAvatarGenerator\InitialAvatar;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property string|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property int|null $current_team_id
 * @property string $locale
 * @property string|null $profile_photo_path
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DeviceUser|\App\Models\SenderIdUser|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SenderId> $activeSenderIds
 * @property-read int|null $active_sender_ids_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sim> $activeSims
 * @property-read int|null $active_sims_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoResponse> $autoResponses
 * @property-read int|null $auto_responses_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Blacklist> $blacklist
 * @property-read int|null $blacklist_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Call> $calls
 * @property-read int|null $calls_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Campaign> $campaigns
 * @property-read int|null $campaigns_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContactList> $contactLists
 * @property-read int|null $contact_lists_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \App\Models\Subscription|null $currentSubscription
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $devices
 * @property-read int|null $devices_count
 * @property-read mixed $label
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Message> $messages
 * @property-read int|null $messages_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Device> $ownedDevices
 * @property-read int|null $owned_devices_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SenderId> $ownedSenderIds
 * @property-read int|null $owned_sender_ids_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read string $profile_photo_url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SenderId> $senderIds
 * @property-read int|null $sender_ids_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SendingServer> $sendingServers
 * @property-read int|null $sending_servers_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sim> $sims
 * @property-read int|null $sims_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Template> $templates
 * @property-read int|null $templates_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\UssdPull> $ussdPulls
 * @property-read int|null $ussd_pulls_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WebhookCall> $webhookCalls
 * @property-read int|null $webhook_calls_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Webhook> $webhooks
 * @property-read int|null $webhooks_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCurrentTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereProfilePhotoPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail, JwtSubject, HasLocalePreference
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Impersonate;
    use HasRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'is_admin',
        'locale',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'label',
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function canImpersonate(): bool
    {
        return $this->is_admin;
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->is_admin;
    }

    /**
     * The devices usable by the user.
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class)->using(DeviceUser::class);
    }

    public function ownedDevices(): HasMany
    {
        return $this->hasMany(Device::class, 'owner_id')->whereEnabled(true);
    }

    public function contactLists(): HasMany
    {
        return $this->hasMany(ContactList::class);
    }

    public function contacts(): HasManyThrough
    {
        return $this->hasManyThrough(Contact::class, ContactList::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class);
    }

    public function autoResponses(): HasMany
    {
        return $this->hasMany(AutoResponse::class);
    }

    public function ussdPulls(): HasMany
    {
        return $this->hasMany(UssdPull::class);
    }

    public function blacklist(): HasMany
    {
        return $this->hasMany(Blacklist::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function sendingServers(): HasMany
    {
        return $this->hasMany(SendingServer::class);
    }

    public function senderIds(): BelongsToMany
    {
        return $this->belongsToMany(SenderId::class)->using(SenderIdUser::class)->withTimestamps();
    }

    public function ownedSenderIds(): HasManyThrough
    {
        return $this->hasManyThrough(SenderId::class, SendingServer::class)
                    ->where('sending_servers.enabled', true);
    }

    public function activeSenderIds(): BelongsToMany
    {
        return $this->senderIds()
                    ->whereRelation('sendingServer', 'enabled', true);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(Webhook::class);
    }

    public function webhookCalls(): HasManyThrough
    {
        return $this->hasManyThrough(WebhookCall::class, Webhook::class);
    }

    public function sims(): HasManyThrough
    {
        return $this->hasManyDeep(Sim::class, [DeviceUser::class, Device::class]);
    }

    public function activeSims(): HasManyThrough
    {
        return $this->sims()
                    ->whereActive(true)
                    ->where('devices.enabled', true);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function currentSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->tap(new CurrentSubscription());
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, Subscription::class);
    }

    protected function label(): Attribute
    {
        return Attribute::make(fn () => "{$this->name} ({$this->email})");
    }

    public function getJwtIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function preferredLocale(): ?string
    {
        return $this->locale;
    }

    protected function defaultProfilePhotoUrl(): string
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));

        try {
            return Cache::rememberForever('avatar-' . Str::slug($name), function () use ($name) {
                $avatar = new InitialAvatar();

                // Use a font file that's within the allowed paths
                // https://github.com/LasseRafn/php-initial-avatar-generator/pull/58
                $fontPath = __DIR__ . '/../../vendor/lasserafn/php-initial-avatar-generator/src/fonts/OpenSans-Regular.ttf';

                $image = $avatar->name($name)
                                ->color('#7F9CF5')
                                ->background('#EBF4FF')
                                ->size(100)
                                ->autoFont()
                                ->font($fontPath)
                                ->generate()
                                ->encode('data-url');

                return $image->encoded;
            });
        } catch (NotSupportedException|MissingDependencyException) {
            return 'https://ui-avatars.com/api/?name='.urlencode($name).'&color=7F9CF5&background=EBF4FF';
        }
    }

    public function canUse(string $feature): Response
    {
        if ($this->is_admin) {
            return Response::allow();
        }

        $allowed = $this->currentSubscription ? $this->currentSubscription->features->{$feature} : false;

        return $allowed
            ? Response::allow()
            : Response::deny(__('messages.global.not_allowed'));
    }

    public function canConsume(int $count, string $feature): Response
    {
        if ($this->is_admin) {
            return Response::allow();
        }

        $limit = $this->currentSubscription ? $this->currentSubscription->features->{$feature} : 0;

        return (is_null($limit) || $count <= $limit)
            ? Response::allow()
            : Response::deny(__('messages.global.limit_exceeded'));
    }

    public function consume(int $credits): bool
    {
        if ($this->is_admin) {
            return true;
        }

        if ($credits === 0) {
            return true;
        }

        $subscription = $this->currentSubscription()->lockForUpdate()->first();

        if (is_null($subscription)) {
            return false;
        }

        if (is_null($subscription->features->credits)) {
            return true;
        }

        if ($subscription->features->credits < $credits) {
            return false;
        }

        $subscription->features->credits -= $credits;
        return $subscription->save();
    }

    /**
     * @throws \Throwable
     */
    public function createUssdPullCampaign(SendCampaignData $data): Campaign
    {
        return DB::transaction(function () use ($data) {
            $count = count($data->payload->ussdCodes);

            $credits = $count * config('saas.credits.ussd_pull.amount');

            abort_unless(
                $this->canConsume($credits, 'credits')->allowed(),
                403,
                __('messages.global.limit_exceeded')
            );

            $attributes = $data->except(
                'sims', 'senderIds', 'attachments', 'contactLists', 'spreadsheet'
            )->toArray();

            $campaign = $this->campaigns()->create($attributes);

            $campaign->attachSims($data->sims);

            if ($count <= 1000) {
                ProcessUssdCampaign::dispatchSync($campaign);
            } else {
                ProcessUssdCampaign::dispatch($campaign);
            }

            return $campaign;
        });
    }

    /**
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Throwable
     */
    public function createMessageCampaign(SendCampaignData $data): Campaign {
        return DB::transaction(function () use ($data) {
            $credits = 0;
            $amount = match ($data->type) {
                CampaignType::Sms => config('saas.credits.sms.amount'),
                CampaignType::Mms => config('saas.credits.mms.amount'),
                default => 0
            };

            $sync = false;
            if ($data->contactLists) {
                $subscribedContacts = Contact::whereIn('contact_list_id', $data->contactLists)
                                             ->where('subscribed', true)
                                             ->whereNotIn('mobile_number', function ($query) {
                                                 $query->select('mobile_number')->from('blacklist')->where('user_id', $this->id);
                                             })
                                             ->count();

                $credits = $subscribedContacts * $amount;

                if ($subscribedContacts <= 1000) {
                    $sync = true;
                }
            } elseif ($data->spreadsheet) {
                $credits = 1;
            } elseif ($data->payload->mobileNumbers) {
                $mobileNumbers = $data->payload->mobileNumbers;
                $count = count($mobileNumbers);

                if ($count <= 1000) {
                    $blacklisted = $this->blacklist()
                                        ->whereIn('mobile_number', $mobileNumbers)
                                        ->count();

                    $allowed = $count - $blacklisted;
                    if ($allowed <= 1000) {
                        $sync = true;
                    }

                    $credits = $allowed * $amount;
                } else {
                    $credits = $count * $amount;
                }
            }

            abort_unless(
                $this->canConsume($credits, 'credits')->allowed(),
                403,
                __('messages.global.limit_exceeded')
            );

            $attributes = $data->except(
                'sims', 'senderIds', 'attachments', 'contactLists', 'spreadsheet'
            )->toArray();

            $campaign = $this->campaigns()->create($attributes);

            if ($data->sims) {
                $campaign->attachSims($data->sims);
            }

            if ($data->senderIds) {
                $campaign->attachSenderIds($data->senderIds);
            }

            if ($data->attachments) {
                foreach ($data->attachments as $attachment) {
                    if (is_string($attachment)) {
                        $campaign->addMediaFromUrl($attachment)->toMediaCollection('attachments');
                    } elseif ($attachment instanceof UploadedFile) {
                        $campaign->addMedia($attachment)->toMediaCollection('attachments');
                    } elseif ($attachment instanceof Media) {
                        $attachment->copy($campaign, 'attachments');
                    }
                }
            }

            if ($data->contactLists) {
                $campaign->contactLists()->sync($data->contactLists);

                if ($sync) {
                    ProcessContactListsCampaign::dispatchSync($campaign);
                } else {
                    ProcessContactListsCampaign::dispatch($campaign);
                }
            } elseif ($data->spreadsheet) {
                $file = $campaign->addMedia($data->spreadsheet)->toMediaCollection('spreadsheet');

                (new MessagesImport($campaign))->queue($file->getPath());
            } else {
                if ($sync) {
                    ProcessManualCampaign::dispatchSync($campaign);
                } else {
                    ProcessManualCampaign::dispatch($campaign);
                }
            }

            return $campaign;
        });
    }

    /**
     * @param  array<int, string>  $except
     */
    public function callWebhooks(WebhookEvent $event, Model $model, array $except = []): void
    {
        $this->webhooks()
             ->forEvent($event)
             ->get()
             ->each(function (Webhook $webhook) use ($event, $model, $except) {
                 $webhook->setRelation('user', $this);
                 $webhook->call($event, $model, $except);
             });
    }
}
