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
use App\Enums\MessageType;
use App\Models\Message;
use App\Models\SendingServer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Rest\Client;

class Twilio implements MessageGateway
{
    private SendingServer $sendingServer;

    public function __construct(SendingServer $sendingServer)
    {
        $this->sendingServer = $sendingServer;
    }

    public static function name(): string
    {
        return 'Twilio';
    }

    public static function supportedTypes(): array
    {
        return ['SMS', 'MMS'];
    }

    public static function fields(): array
    {
        return [
            'sid' => [
                'label' => 'Account SID',
                'type' => 'text',
                'required' => true,
            ],
            'token' => [
                'label' => 'Auth Token',
                'type' => 'text',
                'required' => true
            ],
            'region' => [
                'label' => 'Region',
                'type' => 'text',
                'required' => true
            ],
            'edge' => [
                'label' => 'Edge',
                'type' => 'text',
                'required' => true
            ]
        ];
    }

    /**
     * @throws \Twilio\Exceptions\ConfigurationException
     * @throws \Twilio\Exceptions\TwilioException
     */
    public function send(Message $message): bool
    {
        $client = new Client(
            $this->sendingServer->config->sid,
            $this->sendingServer->config->token,
            null,
            $this->sendingServer->config->region
        );

        $client->setEdge($this->sendingServer->config->edge);

        // Use the Client to make requests to the Twilio REST API
        $response = $client->messages->create(
            // The number you'd like to send the message to
            $message->mobile_number,
            [
                // A Twilio phone number you purchased at https://console.twilio.com
                'from' => $message->from,
                // The body of the text message you'd like to send
                'body' => $message->content,
                'sendAsMms' => $message->type === MessageType::Mms,
            ]
        );

        if ($response->status === 'failed' || $response->status === 'undelivered') {
            $message->response = $response->toArray();

            return false;
        }

        return true;
    }

    public function handleWebhook(Request $request): Response
    {
        Log::debug(json_encode($request->all(), JSON_PRETTY_PRINT));

        return response()->json(['status' => 'success']);
    }
}
