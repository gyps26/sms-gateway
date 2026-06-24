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

namespace App\Imports;

use App\Enums\FieldType;
use App\Models\Contact;
use App\Models\ContactField;
use App\Models\ContactList;
use App\Monitor;
use App\Rules\MobileNumber;
use DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;

class ContactsImport extends TrackedImport
{
    use Importable;

    private ContactList $contactList;

    public function __construct(ContactList $contactList)
    {
        $this->contactList = $contactList;
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    protected function monitor(): Monitor
    {
        return Monitor::for(ContactsImport::class, $this->contactList->id);
    }

    public function prepareForValidation(Collection $collection): void
    {
        foreach ($collection as $row) {
            foreach ($this->contactList->fields as $field) {
                if ($field->type !== FieldType::Checkbox && $field->type !== FieldType::Multiselect) {
                    continue;
                }

                /** @var Collection $row */
                if ($fieldValue = $row->get($field->tag)) {
                    $row[$field->tag] = transform(
                        $fieldValue,
                        fn($value) => array_map('trim', str_getcsv($value))
                    );
                }
            }
        }
    }

    /**
     * @throws \Throwable
     */
    protected function processChunk(Collection $collection): void
    {
        DB::transaction(function () use ($collection) {
            $contacts = [];

            foreach ($collection as $row) {
                $contacts[] = [
                    'mobile_number' => $row['mobile_number'],
                    'subscribed' => $row['subscribed'] ?? true,
                    'contact_list_id' => $this->contactList->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Contact::insert($contacts);

            $contactIds = Contact::whereIn('mobile_number', Arr::pluck($collection, 'mobile_number'))
                                 ->pluck('id', 'mobile_number');

            $contactFields = [];

            foreach ($collection as $row) {
                foreach ($this->contactList->fields as $field) {
                    /** @var Collection $row */
                    if ($row->has($field->tag)) {
                        $contactFields[] = [
                            'contact_id' => $contactIds[$row['mobile_number']],
                            'field_id' => $field->id,
                            'value' => json_encode($row->get($field->tag)),
                        ];
                    }
                }
            }

            if (count($contactFields) > 0) {
                ContactField::insert($contactFields);
            }
        });
    }

    public function rules(): array
    {
        return $this->contactList->fieldsValidationRules() + [
            'mobile_number' => [
                'bail',
                'required',
                new MobileNumber(),
                Rule::unique('contacts', 'mobile_number')
                    ->where('contact_list_id', $this->contactList->id)
            ],
            'subscribed' => ['boolean']
        ];
    }
}
