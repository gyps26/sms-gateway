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

use App\Enums\FrequencyUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $value
 * @property int $frequency
 * @property FrequencyUnit $frequency_unit
 * @property int $available
 * @property \Illuminate\Support\Carbon $reset_at
 * @property bool $enabled
 * @property string $quotable_type
 * @property int $quotable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model|\Eloquent $quotable
 * @method static \Database\Factories\QuotaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereFrequency($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereFrequencyUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereQuotableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereQuotableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereResetAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Quota whereValue($value)
 * @mixin \Eloquent
 */
class Quota extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'frequency' => 'integer',
            'value' => 'integer',
            'available' => 'integer',
            'quotable_id' => 'integer',
            'reset_at' => 'datetime',
            'frequency_unit' => FrequencyUnit::class,
        ];
    }

    public function quotable(): MorphTo
    {
        return $this->morphTo();
    }

    public function reset(): void
    {
        if ($this->reset_at > now()) {
            return;
        }

        $this->update([
            'available' => $this->value,
            'reset_at' => $this->reset_at->add($this->frequency, $this->frequency_unit->value),
        ]);
    }
}
