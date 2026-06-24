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
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class Custom implements MessageGateway
{
    private SendingServer $sendingServer;

    public function __construct(SendingServer $sendingServer)
    {
        $this->sendingServer = $sendingServer;
    }

    public static function name(): string
    {
        return 'Custom';
    }

    public static function fields(): array
    {
        return [
            'url' => [
                'label' => 'URL',
                'type' => 'text',
                'required' => true
            ],
            'method' => [
                'label' => 'Method',
                'type' => 'list',
                'options' => [
                    'GET',
                    'POST'
                ],
                'default' => 'POST',
                'required' => true
            ],
            'headers' => [
                'label' => 'Headers',
                'type' => 'dictionary',
                'default' => [
                    [
                        'key' => 'Content-Type',
                        'value' => 'application/x-www-form-urlencoded'
                    ]
                ],
                'required' => false
            ],
            'params' => [
                'label' => 'Parameters',
                'type' => 'dictionary',
                'default' => [
                    [
                        'key' => 'from',
                        'value' => '{sender_id}'
                    ],
                    [
                        'key' => 'to',
                        'value' => '{mobile_number}'
                    ],
                    [
                        'key' => 'message',
                        'value' => '{message}'
                    ],
                    [
                        'key' => 'attachments',
                        'value' => '{attachments}'
                    ],
                    [
                        'key' => 'type',
                        'value' => '{type}'
                    ]
                ],
                'required' => true
            ],
            'success_parameter' => [
                'label' => 'Success Parameter',
                'type' => 'text',
                'required' => false
            ],
            'success_value' => [
                'label' => 'Success Value',
                'type' => 'text',
                'required' => false
            ]
        ];
    }

    public static function supportedTypes(): array
    {
        return ['SMS', 'MMS', 'WhatsApp'];
    }

    public function send(Message $message): bool
    {
        $url = $this->sendingServer->config->url;
        $method = $this->sendingServer->config->method;
        $headers = $this->sendingServer->config->headers;
        $params = $this->sendingServer->config->params;

        $headers = collect($headers)->mapWithKeys(function ($header) {
            return [$header['key'] => $header['value']];
        })->toArray();

        $asJson = Arr::any(
            $headers,
            function ($value, $key) {
                return strcasecmp($key, 'Content-Type') === 0 && strcasecmp($value, 'application/json') === 0;
            }
        );

        $params = collect($params)
            ->mapWithKeys(
                function ($param) use ($message) {
                    $value = str_replace(
                        search: [
                            '{sender_id}',
                            '{mobile_number}',
                            '{message}',
                            '{type}',
                            '{attachments}'
                        ],
                        replace: [
                            $message->from,
                            $message->to,
                            $message->content,
                            Str::lower($message->type->value),
                            $message->getMedia('attachments')->map(fn($media) => $media->getUrl())->join(',')
                        ],
                        subject: $param['value']
                    );

                    return [$param['key'] => $value];
                }
            )
            ->when($asJson, fn($collection) => $collection->undot())
            ->toArray();

        $pendingRequest = $asJson ? Http::asJson() : Http::asForm();

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = $pendingRequest->withHeaders($headers)->{$method}($url, $params);

            if ($response->successful()) {
                $parameter = $this->sendingServer->config->get('success_parameter');
                $expected = $this->sendingServer->config->get('success_value') ?? true;

                if (is_null($parameter)) {
                    return true;
                }

                if ($response->json($parameter) == $expected) {
                    return true;
                }
            }

            $message->response = $response->json() ?? ['message' => (new RequestException($response))->getMessage()];
            return false;
        } catch (ConnectionException) {
            return false;
        }
    }

    public function handleWebhook(Request $request): Response
    {
        abort(404);
    }
}
