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

use App\Enums\CampaignableStatus;
use App\Enums\CampaignStatus;
use App\Observers\CampaignableObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
use Staudenmeir\EloquentJsonRelations\Relations\BelongsToJson;

#[ObservedBy(CampaignableObserver::class)]
/**
 * @property int $id
 * @property array<array-key, mixed> $senders
 * @property \Illuminate\Support\Carbon|null $resume_at
 * @property CampaignableStatus $status
 * @property int $campaign_id
 * @property string $campaignable_type
 * @property int $campaignable_id
 * @property-read \App\Models\Campaign $campaign
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $campaignable
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sim[] $sims
 * @property-read int|null $sims_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SenderId[] $senderIds
 * @property-read int|null $sender_ids_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereCampaignableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereCampaignableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereResumeAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereSenders($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Campaignable whereStatus($value)
 * @mixin \Eloquent
 */
class Campaignable extends MorphPivot
{
    use HasJsonRelationships;

    public $timestamps = false;

    protected $table = 'campaignables';

    public function casts(): array
    {
        return [
            'senders' => 'array',
            'status' => CampaignableStatus::class,
            'resume_at' => 'datetime',
            'campaign_id' => 'integer',
            'campaignable_id' => 'integer',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function campaignable(): MorphTo
    {
        return $this->morphTo();
    }

    public function sims(): BelongsToJson
    {
        return $this->belongsToJson(Sim::class, 'senders->sims');
    }

    public function senderIds(): BelongsToJson
    {
        return $this->belongsToJson(SenderId::class, 'senders->sender_ids');
    }

    public function send(): void
    {
        $this->update([
            'status' => CampaignableStatus::Pending,
            'resume_at' => null
        ]);

        $this->campaign->update([
            'status' => CampaignStatus::Processed,
        ]);

        $this->campaignable->send($this->campaign);
    }
}
