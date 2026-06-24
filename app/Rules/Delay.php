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

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Delay implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $delays = explode('-', $value);
        if (count($delays) <= 2) {
            foreach ($delays as $delay) {
                if (ctype_digit($delay)) {
                    if ($delay > 120) {
                        $fail(__('validation.custom.delay.range'));
                        return;
                    }
                } else {
                    $fail(__('validation.custom.delay.invalid'));
                    return;
                }
            }

            if (count($delays) !== 2 || $delays[1] > $delays[0]) {
                return;
            }
        }
        $fail(__('validation.custom.delay.invalid'));
    }
}
