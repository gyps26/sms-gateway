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

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property array<array-key, mixed>|null $value
 * @property int $contact_id
 * @property int $field_id
 * @property-read \App\Models\Contact $contact
 * @property-read \App\Models\Field $field
 * @method static \Database\Factories\ContactFieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField whereFieldId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactField whereValue($value)
 * @mixin \Eloquent
 */
class ContactField extends Pivot
{
    use HasFactory;

    public $timestamps = false;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'value' => 'json',
            'contact_id' => 'integer',
            'field_id' => 'integer',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function field(): BelongsTo
    {
        return $this->belongsTo(Field::class);
    }
}
