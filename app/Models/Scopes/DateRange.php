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

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

readonly class DateRange
{
    /**
     * Create a new class instance.
     */
    public function __construct(private ?string $after, private ?string $before, private ?string $column = 'sent_at')
    {
        //
    }

    /**
     * Invoke the class instance.
     */
    public function __invoke(Builder $query): void
    {
        $query->when($this->after, fn($query, $value) => $query->where($this->column, '>=', Carbon::parse($value)->startOfDay()))
              ->when($this->before, fn($query, $value) => $query->where($this->column, '<=', Carbon::parse($value)->endOfDay()));
    }
}
