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
use App\Observers\ContactListObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

#[ObservedBy([ContactListObserver::class])]
/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Contact> $contacts
 * @property-read int|null $contacts_count
 * @property-read mixed $export_path
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Field> $fields
 * @property-read int|null $fields_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\ContactListFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ContactList whereUserId($value)
 * @mixin \Eloquent
 */
class ContactList extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(Field::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    protected function exportPath(): Attribute
    {
        return Attribute::make(
            get: function () {
                return "public" . DIRECTORY_SEPARATOR . hash('sha256', "{$this->name} #{$this->id}") . ".xlsx";
            }
        );
    }

    public function fieldsValidationRules(): array
    {
        $rules = [];
        $this->fields->each(function ($field) use (&$rules) {
            $rules += [$field->tag => [$field->required ? 'required' : 'nullable']];

            switch ($field->type) {
                case FieldType::Text:
                case FieldType::Textarea:
                    $rules[$field->tag][] = 'string';
                    break;
                case FieldType::Email:
                    $rules[$field->tag][] = 'email';
                    break;
                case FieldType::Number:
                    $rules[$field->tag][] = 'numeric';
                    break;
                case FieldType::Dropdown:
                case FieldType::Radio:
                    $rules[$field->tag][] = Rule::in(Arr::pluck($field->options, 'value'));
                    break;
                case FieldType::Multiselect:
                case FieldType::Checkbox:
                    $rules[$field->tag][] = 'array';
                    $rules["{$field->tag}.*"] = ['distinct', Rule::in(Arr::pluck($field->options, 'value'))];
                    break;
                case FieldType::Date:
                    $rules[$field->tag][] = 'date_format:Y-m-d';
                    break;
                case FieldType::DatetimeLocal:
                    $rules[$field->tag][] = 'date';
                    break;
                case FieldType::Time:
                    $rules[$field->tag][] = 'date_format:H:i';
                    break;
            }
        });

        return $rules;
    }

    /**
     * @param  array<string, string>  $data
     *
     * @throws \Throwable
     */
    public function addContact(array $data): Contact
    {
        return
            DB::transaction(function () use ($data) {
                /** @var Contact $contact */
                $contact = $this->contacts()->create([
                    'mobile_number' => $data['mobile_number'],
                    'subscribed' => $data['subscribed'] ?? true,
                ]);

                foreach ($this->fields as $field) {
                    if (array_key_exists($field->tag, $data)) {
                        $value = data_get($data, $field->tag);
                        $contact->contactFields()->create(['field_id' => $field->id, 'value' => $value]);
                    }
                }

                return $contact;
            });
    }
}
