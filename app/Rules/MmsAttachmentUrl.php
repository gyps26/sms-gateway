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
use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class MmsAttachmentUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        try {
            $response = Http::head($value)->throw();
            $contentType = $response->header('Content-Type');

            $allowed = [
                'image/jpeg', 'image/png', 'image/gif', 'audio/aac', 'video/3gpp', 'audio/amr', 'audio/mpeg',
                'audio/mp4', 'audio/wav', 'video/mp4', 'text/plain', 'text/vcard', 'text/html'
            ];

            // Check if the Content-Type header is set and matches one of the allowed MIME types
            if (! empty($contentType) && in_array($contentType, $allowed)) {
                return;
            }

            $fail(__('validation.custom.mms_attachment_url.file_type'));
        } catch (Exception) {
            $fail(__('validation.custom.mms_attachment_url.invalid'));
        }
    }
}
