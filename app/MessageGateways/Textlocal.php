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

namespace App\MessageGateways;

use App\Contracts\MessageGateway;
use App\Models\Message;
use App\Models\SendingServer;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class Textlocal implements MessageGateway
{
    private SendingServer $sendingServer;

    public function __construct(SendingServer $sendingServer)
    {
        $this->sendingServer = $sendingServer;
    }

    public static function name(): string
    {
        return 'Textlocal';
    }

    public static function fields(): array
    {
        return [
            'api_key' => [
                'label' => 'API Key',
                'type' => 'text',
                'required' => true
            ],
            'url' => [
                'label' => 'URL',
                'type' => 'text',
                'required' => true
            ],
            'test' => [
                'label' => 'Test',
                'type' => 'boolean',
                'required' => true
            ]
        ];
    }

    public static function supportedTypes(): array
    {
        return ['SMS'];
    }

    public function send(Message $message): bool
    {
        $data = [
            'sender' => $message->from,
            'numbers' => $message->to,
            'message' => rawurlencode($message->content),
            'apiKey' => $this->sendingServer->config->api_key,
            'custom' => $message->id,
            'test' => $this->sendingServer->config->test,
            'receipt_url' => route('sending-servers.webhook', $this->sendingServer->id),
        ];

        try {
            $response = Http::asForm()->acceptJson()->post($this->sendingServer->config->url . '/send', $data);

            $result = $response->json('status') === 'success';

            if (! $result) {
                $message->response = $response->json();
            }

            return $result;
        } catch (ConnectionException) {
            return false;
        }
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug(json_encode($request->all(), JSON_PRETTY_PRINT));

        return response()->json(['status' => 'success']);
    }
}
