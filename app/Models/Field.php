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

use App\Enums\FieldType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $label
 * @property string $tag
 * @property FieldType $type
 * @property array<array-key, mixed>|null $options
 * @property array<array-key, mixed>|null $default_value
 * @property bool $required
 * @property int $contact_list_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContactField> $contactFields
 * @property-read int|null $contact_fields_count
 * @property-read \App\Models\ContactList $contactList
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereContactListId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDefaultValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Field extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'contact_list_id' => 'integer',
            'type' => FieldType::class,
            'default_value' => 'json',
            'options' => 'array',
            'required' => 'boolean'
        ];
    }

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }

    public function contactFields(): HasMany
    {
        return $this->hasMany(ContactField::class);
    }
}
