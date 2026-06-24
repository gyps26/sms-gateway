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

use App\Exceptions\CancellationException;
use App\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use Maatwebsite\Excel\Concerns\RemembersChunkOffset;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

abstract class TrackedImport extends DefaultValueBinder implements ToCollection, WithChunkReading, WithCustomValueBinder, WithEvents, WithHeadingRow, ShouldQueue
{
    use InteractsWithQueue;
    use RemembersChunkOffset;

    private Monitor $monitor;

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function bindValue(Cell $cell, $value): bool
    {
        if (is_string($value) && (str_starts_with($value, '+') || ctype_digit($value))) {
            $cell->setValueExplicit($value);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /**
     * @throws \App\Exceptions\CancellationException
     */
    public function collection(Collection $collection): void
    {
        $this->prepareForValidation($collection);

        $count = $collection->count();

        $failures = [];
        foreach ($collection as $index => $row) {
            if ($this->monitor->cancelled) {
                $this->monitor->logger()->info('Import cancelled.');
                $this->monitor->clear();
                throw new CancellationException();
            }

            $rowIndex = $this->getChunkOffset() + $index;

            try {
                $validator = validator(
                    $row->toArray(),
                    $this->rules(),
                    $this->customValidationMessages(),
                    $this->customValidationAttributes()
                );

                $this->withValidator($validator, $collection);

                $validator->validate();
            } catch (ValidationException $e) {
                $errors = ['row' => $rowIndex, 'failures' => []];
                foreach ($e->errors() as $attribute => $messages) {
                    $errors['failures'][] = [
                        'attribute' => $attribute,
                        'value' => $row->get($attribute),
                        'errors' => $messages,
                    ];
                }

                $this->monitor->logger()->error(json_encode($errors, JSON_PRETTY_PRINT));

                $failures[] = $index;
            }
        }

        $this->processChunk($collection->forget($failures));

        $this->monitor->failures += count($failures);
        $this->monitor->processed += $count;
    }

    abstract protected function processChunk(Collection $collection): void;

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function (BeforeImport $event) {
                $this->monitor = $this->monitor();
                $this->monitor->total = transform($event->getReader()->getTotalRows(), fn($value) => array_sum($value) - count($value));
                $this->monitor->processed = $this->monitor->failures = 0;
                $this->monitor->logger()->info('Import is queued for processing…');
            },
            BeforeSheet::class  => function (BeforeSheet $event) {
                if ($this->monitor->processed === 0) {
                    $this->monitor->logger()->info('Import process started.');
                    $this->beforeImport();
                }
            },
            AfterSheet::class   => function (AfterSheet $event) {
                if ($this->monitor->processed > 0) {
                    $this->monitor->logger()
                                  ->info("Processed: {$this->monitor->processed}/{$this->monitor->total} records, failures: {$this->monitor->failures} records.");
                }
            },
            AfterImport::class  => function () {
                $this->monitor->logger()->info('Import process finished.');
                $this->afterImport();
            }
        ];
    }

    abstract protected function monitor(): Monitor;

    protected function rules(): array
    {
        return [];
    }

    protected function prepareForValidation(Collection $collection): void
    {
        //
    }

    protected function withValidator(Validator $validator, Collection $collection): void
    {
        //
    }

    protected function customValidationMessages(): array
    {
        return [];
    }

    protected function customValidationAttributes(): array
    {
        return [];
    }

    protected function beforeImport(): void
    {
        //
    }

    protected function afterImport(): void
    {
        //
    }
}
