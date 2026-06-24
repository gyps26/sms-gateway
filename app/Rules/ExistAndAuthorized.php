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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Throwable;

class ExistAndAuthorized implements ValidationRule
{
    public function __construct(protected string $relation, protected string $primaryAttribute = 'id')
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = Arr::wrap($value);

        if (empty($value)) {
            return;
        }

        if (in_array('*', $value)) {
            if (Auth::user()->{$this->relation}()->exists()) {
                return;
            }
        } else {
            try {
                $result = Auth::user()
                              ->{$this->relation}()
                              ->whereIn($this->primaryAttribute, $value)
                              ->count() === count($value);
            } catch (Throwable) {
                $result = false;
            }

            if ($result) {
                return;
            }
        }

        $fail(trans_choice('validation.custom.exists_and_authorized', count($value)));
    }

    public static function docs(): array
    {
        return [
            'description' => 'It must be an array of existing and authorized resources.',
            'example' => [1, 2, 3, 4, 5],
        ];
    }
}
