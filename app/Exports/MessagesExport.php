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

use App\Data\Filters\MessageFiltersData;
use App\Models\Message;
use App\Monitor;
use Auth;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Throwable;

class MessagesExport extends DefaultValueBinder implements WithColumnFormatting, WithCustomValueBinder, WithEvents, FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, ShouldQueue
{
    use Exportable;

    private MessageFiltersData $filters;

    private Monitor $monitor;

    public function headings(): array
    {
        return [
            'From',
            'To',
            'Content',
            'Status',
            'Sent At',
            'Delivered At',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function forFilters(MessageFiltersData $filters): static
    {
        $this->filters = $filters;
        $this->monitor = Monitor::for(MessagesExport::class, Auth::id());
        if ($this->monitor->processed === $this->monitor->total) {
            $this->monitor->clear();
            File::delete(Storage::path(Message::exportFile()));
        }
        $this->monitor->total = $this->query()->count();
        $this->monitor->processed = $this->monitor->failures = 0;
        $this->monitor->status = 'Queued';
        return $this;
    }

    public function query(): Builder
    {
        return Message::filter($this->filters);
    }

    /**
     * @throws \Exception
     */
    public function map(mixed $row): array
    {
        $this->monitor->processed++;

        return [
            $row->from,
            $row->to,
            $row->content,
            $row->status->value,
            Date::dateTimeToExcel($row->sent_at),
            $row->delivered_at ? Date::dateTimeToExcel($row->delivered_at) : null
        ];
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
        }

        return parent::bindValue($cell, $value);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => 'yyyy-mm-dd hh:mm:ss AM/PM',
            'F' => 'yyyy-mm-dd hh:mm:ss AM/PM'
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $this->monitor->status = 'Processing';
            },
        ];
    }
}
