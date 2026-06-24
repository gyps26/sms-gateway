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

namespace App\Contracts;

use App\Models\Message;
use App\Models\SendingServer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

interface MessageGateway
{
    public function __construct(SendingServer $sendingServer);

    public static function name(): string;

    /**
     * Array of fields with each field having label, type and options (optional) keys.
     * The type can be text, number, boolean, list or dictionary.
     * The options key is required when you set the type of field to list.
     * Users will be able to choose from the list of options you provide.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function fields(): array;

    /**
     * Array of supported message types. The type can be SMS, MMS or WhatsApp.
     *
     * @return array<int, string>
     */
    public static function supportedTypes(): array;

    public function send(Message $message): bool;

    public function handleWebhook(Request $request): Response;
}
