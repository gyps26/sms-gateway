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
use Closure;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File as FileValidation;

class FirebaseSettingsData extends BaseData
{
    public function __construct(
        public UploadedFile $serviceAccountJson,
    ) {}

    public static function rules(): array
    {
        return [
            'service_account_json' => [
                'required',
                FileValidation::types('json'),
                function (string $attribute, mixed $value, Closure $fail) {
                    if (Str::isJson($value->getContent())) {
                        $array = json_decode($value->getContent(), true);
                        if (is_array($array)) {
                            $keys = [
                                'type',
                                'project_id',
                                'private_key_id',
                                'private_key',
                                'client_email',
                                'client_id',
                                'auth_uri',
                                'token_uri',
                                'auth_provider_x509_cert_url',
                                'client_x509_cert_url'
                            ];

                            if (Arr::has($array, $keys) && array_reduce($array, fn($carry, $item) => $carry && is_string($item), true)) {
                                return;
                            }
                        }
                    }

                    $fail('The :attribute is invalid.');
                }
            ],
        ];
    }

    public static function authorize(): bool
    {
        return Gate::allows('updateAny', Setting::class);
    }
}
