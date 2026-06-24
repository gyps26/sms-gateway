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

namespace App\Rules;

use App\ChunkReadFilter;
use App\Helpers\Validators;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

class Spreadsheet implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $reader = IOFactory::createReaderForFile($value->path());
            $chunkFilter = new ChunkReadFilter(2, 5);
            $reader->setReadFilter($chunkFilter);
            $spreadsheet = $reader->load($value->path());
            $data = $spreadsheet->getActiveSheet()->toArray();
            $firstCell = Str::slug(data_get($data, '0.0'), '_');
            if (Arr::first($data, fn($row, $key) => Validators::isMobileNumber($row[0])) === null || $firstCell !== 'mobile_number') {
                $fail(__('validation.custom.spreadsheet'));
            }
        } catch (Throwable) {
            $fail(__('validation.custom.spreadsheet'));
        }
    }
}
