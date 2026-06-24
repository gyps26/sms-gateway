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

namespace App\Http\Requests;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateMailerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('updateAny', Setting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'mailer' => ['required', 'string', 'in:log,sendmail,smtp,mailgun,ses,postmark,resend'],
            'sendmail.path' => ['required_if:mailer,sendmail', 'exclude_unless:mailer,sendmail', 'string'],
            'smtp.host' => ['exclude_unless:mailer,smtp', 'required_if:mailer,smtp', 'string'],
            'smtp.port' => ['exclude_unless:mailer,smtp', 'required_if:mailer,smtp', 'integer'],
            'smtp.encryption' => ['exclude_unless:mailer,smtp', 'required_if:mailer,smtp', 'string'],
            'smtp.username' => ['exclude_unless:mailer,smtp', 'required_if:mailer,smtp', 'string'],
            'smtp.password' => ['exclude_unless:mailer,smtp', 'required_if:mailer,smtp', 'string'],
            'mailgun.domain' => ['exclude_unless:mailer,mailgun', 'required_if:mailer,mailgun', 'string'],
            'mailgun.secret' => ['exclude_unless:mailer,mailgun', 'required_if:mailer,mailgun', 'string'],
            'mailgun.endpoint' => ['exclude_unless:mailer,mailgun', 'required_if:mailer,mailgun', 'string'],
            'ses.key' => ['exclude_unless:mailer,ses', 'required_if:mailer,ses', 'string'],
            'ses.secret' => ['exclude_unless:mailer,ses', 'required_if:mailer,ses', 'string'],
            'ses.region' => ['exclude_unless:mailer,ses', 'required_if:mailer,ses', 'string'],
            'postmark.token' => ['exclude_unless:mailer,postmark', 'required_if:mailer,postmark', 'string'],
            'resend.key' => ['exclude_unless:mailer,resend', 'required_if:mailer,resend', 'string'],
            'message_to_email' => ['required', 'boolean'],
        ];
    }
}
