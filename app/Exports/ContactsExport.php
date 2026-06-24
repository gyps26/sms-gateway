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

namespace App\Exports;

use App\Models\Contact;
use App\Models\ContactList;
use App\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\BeforeExport;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class ContactsExport extends DefaultValueBinder implements WithColumnFormatting, WithCustomValueBinder, WithEvents, FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, ShouldQueue
{
    use Exportable;

    private ContactList $contactList;

    private Monitor $monitor;

    public function headings(): array
    {
        $headings = [
            'Mobile Number',
            'Subscribed',
        ];

        $this->contactList->fields->each(function ($field, $key) use (&$headings) {
            $headings[] = $field->label;
        });

        return array_merge($headings, [
            'Created At',
            'Updated At',
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A' => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @param  \App\Models\ContactList  $contactList
     * @return $this
     */
    public function forContactList(ContactList $contactList): static
    {
        $this->contactList = $contactList;
        $this->monitor = Monitor::for(ContactsExport::class, $contactList->id);
        $this->monitor->total = $this->query()->count();
        $this->monitor->processed = $this->monitor->failures = 0;
        $this->monitor->status = 'Queued';
        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return Contact::with('contactFields')->whereContactListId($this->contactList->id);
    }

    /**
     * @param  mixed  $row
     * @return array
     * @throws \Exception
     */
    public function map($row): array
    {
        $this->monitor->processed++;

        $mapping = [
            $row->mobile_number,
            $row->subscribed ? 'Yes' : 'No'
        ];

        $row->setRelation('contactList', $this->contactList);

        $this->contactList->fields->each(function ($field, $key) use ($row, &$mapping) {
            if (in_array($field->type, ['datetime-local', 'date'])) {
                $mapping[] = Date::dateTimeToExcel(new Carbon($row->extras[$field->tag]));
            } else {
                $mapping[] = $row->extras[$field->tag];
            }
        });

        return array_merge($mapping, [
            Date::dateTimeToExcel($row->created_at),
            Date::dateTimeToExcel($row->updated_at)
        ]);
    }

    public function failed(Throwable $exception): void
    {
        $this->monitor->status = 'Failed';
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value) && str_starts_with($value, '+')) {
            $cell->setValueExplicit($value);
            return true;
        } else if (is_array($value)) {
            $value = implode(', ', $value);
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        $columnFormats = [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
        ];

        $index = count($columnFormats);
        $this->contactList->fields->each(function ($field, $key) use (&$index, &$columnFormats) {
            $nextColumn = Coordinate::stringFromColumnIndex(++$index);
            $columnFormats[$nextColumn] = match ($field->type) {
                'number' => NumberFormat::FORMAT_NUMBER,
                'date' => NumberFormat::FORMAT_DATE_YYYYMMDD,
                'datetime-local' => 'yyyy-mm-dd hh:mm AM/PM',
                default => NumberFormat::FORMAT_TEXT,
            };
        });

        $columnFormats[Coordinate::stringFromColumnIndex(++$index)] = 'yyyy-mm-dd hh:mm:ss AM/PM';
        $columnFormats[Coordinate::stringFromColumnIndex(++$index)] = 'yyyy-mm-dd hh:mm:ss AM/PM';

        return $columnFormats;
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function(BeforeExport $event) {
                $this->monitor->status = 'Processing';
            },
        ];
    }
}
