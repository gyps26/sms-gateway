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

namespace App\Data\Settings;

use App\Data\BaseData;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File as FileValidation;
use Spatie\LaravelData\Attributes\Validation\File;
use Spatie\LaravelData\Attributes\Validation\Url;

class GeneralSettingsData extends BaseData
{
    public function __construct(
        public string $name,
        public ?UploadedFile $logo,
        #[Url(['http', 'https', 'mailto', 'tel'])]
        public string $supportUrl,
        #[File]
        public ?UploadedFile $app,
        public bool $homepage,
    ) {}

    public static function rules(): array
    {
        return [
            'logo' => [
                'nullable',
                FileValidation::types(['png', 'jpg', 'jpeg', 'svg'])->max(1024),
                Rule::dimensions()->height(512)->width(512)
            ]
        ];
    }

    public static function authorize(): bool
    {
        return Gate::allows('updateAny', Setting::class);
    }
}
