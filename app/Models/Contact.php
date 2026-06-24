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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $mobile_number
 * @property bool $subscribed
 * @property int $contact_list_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ContactField> $contactFields
 * @property-read int|null $contact_fields_count
 * @property-read \App\Models\ContactList $contactList
 * @property-read mixed $extras
 * @property-read \App\Models\ContactField|null $pivot
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @method static \Database\Factories\ContactFactory factory($count = null, $state = [])
 * @method static Builder<static>|Contact newModelQuery()
 * @method static Builder<static>|Contact newQuery()
 * @method static Builder<static>|Contact query()
 * @method static Builder<static>|Contact search(string $search)
 * @method static Builder<static>|Contact whereContactListId($value)
 * @method static Builder<static>|Contact whereCreatedAt($value)
 * @method static Builder<static>|Contact whereId($value)
 * @method static Builder<static>|Contact whereMobileNumber($value)
 * @method static Builder<static>|Contact whereSubscribed($value)
 * @method static Builder<static>|Contact whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contact extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relations to eager load on every query.
     *
     * @var array<int, string>
     */
    protected $with = ['contactFields'];

    protected function casts(): array
    {
        return [
            'subscribed' => 'boolean',
            'contact_list_id' => 'integer',
        ];
    }

    protected function extras(): Attribute
    {
        return Attribute::make(get: function () {
            $extras = [];
            foreach ($this->contactList->fields as $field) {
                $contactField = $this->contactFields->first(fn($contactField) => $contactField->field_id === $field->id);
                $extras[$field->tag] = $contactField ? $contactField->value : $field->default_value;
            }

            return $extras;
        });
    }

    public function contactList(): BelongsTo
    {
        return $this->belongsTo(ContactList::class);
    }

    public function fields()
    {
        return $this->belongsToMany(Field::class)->using(ContactField::class)->withPivot(['value']);
    }

    public function contactFields(): HasMany
    {
        return $this->hasMany(ContactField::class);
    }

    public function scopeSearch(Builder $query, string $search): void
    {
        $query->where('contacts.mobile_number', 'like', "%{$search}%")
              ->orWhereHas('contactFields', function ($subQuery) use ($search) {
                  return $subQuery->where('value', 'like', "%{$search}%");
              });
    }

    /**
     * @param  array<string, string>  $data
     *
     * @throws \Throwable
     */
    public function modify(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->update([
                'mobile_number' => $data['mobile_number'],
                'subscribed' => $data['subscribed'],
            ]);

            foreach ($this->contactList->fields as $field) {
                if (array_key_exists($field->tag, $data)) {
                    $value = data_get($data, $field->tag);
                    $this->contactFields()->updateOrCreate(['field_id' => $field->id], ['value' => $value]);
                }
            }
        });
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>|string  $phoneNumbers
     *
     * @return \Illuminate\Support\Collection<int, string>|string|null
     */
    public static function identify(Collection|string $phoneNumbers): Collection|string|null
    {
        $collection = Collection::wrap($phoneNumbers);

        $result = collect();

        if ($collection->isNotEmpty() && config('messaging.phone_id.enabled')) {
            $phoneId = Setting::retrieve(
                'messaging.phone_id',
                Auth::id(),
                config('messaging.phone_id')
            );

            if ($phoneId['enabled']) {
                $fields = Field::where('tag', $phoneId['contact_field_tag'])
                               ->whereRelation('contactList', 'user_id', Auth::id())
                               ->pluck('fields.id');

                if ($fields->isNotEmpty()) {
                    $result = ContactField::whereIn('field_id', $fields)
                                          ->join('contacts', 'contact_field.contact_id', '=', 'contacts.id')
                                          ->whereIn('contacts.mobile_number', $collection)
                                          ->pluck('contact_field.value', 'contacts.mobile_number');
                }
            }
        }

        return $phoneNumbers instanceof Enumerable ? $result : $result->first();
    }
}
