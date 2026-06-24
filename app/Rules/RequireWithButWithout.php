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
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

class RequireWithButWithout implements DataAwareRule, ValidationRule
{
    protected array $with;
    protected array $without;

    /**
     * Indicates whether the rule should be implicit.
     *
     * @var bool
     */
    public bool $implicit = true;

    /**
     * All the data under validation.
     *
     * @var array<string, mixed>
     */
    protected array $data = [];

    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function __construct(string|array $with, string|array $without)
    {
        $this->with = Arr::wrap($with);
        $this->without = Arr::wrap($without);
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $shouldBePresent = false;

        foreach ($this->with as $withField) {
            if (data_get($this->data, $withField)) {
                $shouldBePresent = true;
                break;
            }
        }

        foreach ($this->without as $withoutField) {
            if (data_get($this->data, $withoutField)) {
                $shouldBePresent = false;
                break;
            }
        }

        $getName = function ($attribute) {
            return Lang::has("validation.attributes.{$attribute}")
                ? __("validation.attributes.{$attribute}")
                : mb_strtolower(Str::headline($attribute));
        };

        if ($shouldBePresent && is_null($value)) {
            $fail(
                __('validation.custom.require_with_but_without', [
                    'with' => Arr::join(Arr::map($this->with, $getName), ', '),
                    'without' => Arr::join(Arr::map($this->without, $getName), ', '),
                ])
            );
        }
    }
}
