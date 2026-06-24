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

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;

class Calculate
{
    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tax>  $taxes
     */
    public static function amount(float $amount, int $quantity, Collection $taxes, ?int $discount): float|int
    {
        $grossTotal = $quantity * $amount;

        // Sum inclusive tax rates
        $inclusiveTaxRate = $taxes->where('inclusive', true)->sum('rate');

        // Remove inclusive taxes from gross total
        $grossTotal = ($grossTotal * 100) / (100 + $inclusiveTaxRate);

        $netTotal = $grossTotal;
        if ($discount) {
            $netTotal = $netTotal - ($grossTotal * $discount) / 100;
        }

        // Add all taxes (on discounted amount)
        $taxTotal = $taxes->sum(fn($tax) => ($netTotal * $tax->rate) / 100);

        return $netTotal + $taxTotal;
    }
}
