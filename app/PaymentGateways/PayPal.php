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

namespace App\PaymentGateways;

use App\Contracts\PaymentGateway;
use App\Enums\IntervalUnit;
use App\Helpers\Calculate;
use App\Jobs\ProcessPayPalWebhookJob;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\WebhookRequest;
use App\SignatureValidators\PayPalSignatureValidator;
use App\WebhookProfiles\PayPalWebhookProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo;
use Symfony\Component\HttpFoundation\Response;

class PayPal implements PaymentGateway
{
    // https://developer.paypal.com/reference/currency-codes/
    const TWO_DECIMAL_CURRENCIES = [
        'AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'ILS', 'MYR', 'MXN', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'SGD',
        'SEK', 'CHF', 'THB', 'USD'
    ];

    const ZERO_DECIMAL_CURRENCIES = [
        'HUF', 'JPY', 'TWD'
    ];

    private string $clientId;

    private string $clientSecret;

    private string $accessToken;

    private string $url;

    private string $productId;

    private string $webhookId;

    public function __construct()
    {
        $this->clientId = config('payment-gateways.PayPal.client_id');
        $this->clientSecret = config('payment-gateways.PayPal.client_secret');
        $this->webhookId = config('payment-gateways.PayPal.webhook_id');

        $this->url = config('payment-gateways.PayPal.sandbox') ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
        $this->productId = Str::slug(config('app.name'));

        $this->accessToken = Cache::get('payment-gateways.PayPal.access_token', function () {
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                            ->asForm()
                            ->post("$this->url/v1/oauth2/token", [
                                'grant_type' => 'client_credentials',
                            ])
                            ->onError(function ($response) {
                                abort($response->status(), $response->body());
                            });

            $body = $response->json();
            $accessToken = $body['access_token'];
            $expiresIn = $body['expires_in'];

            Cache::put('payment-gateways.PayPal.access_token', $accessToken, $expiresIn);

            return $accessToken;
        });
    }

    public static function label(): string
    {
        return 'PayPal';
    }

    public static function fields(): array
    {
        return [
            'sandbox' => [
                'label' => 'Sandbox',
                'type' => 'boolean'
            ],
            'client_id' => [
                'label' => 'Client ID',
                'type' => 'text'
            ],
            'client_secret' => [
                'label' => 'Client Secret',
                'type' => 'text'
            ],
            'webhook_id' => [
                'label' => 'Webhook ID',
                'type' => 'text'
            ]
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tax>  $taxes
     * @param  array<string, string>  $billingInfo
     */
    public function initPayment(
        Plan $plan,
        Collection $taxes,
        ?Coupon $coupon,
        array $billingInfo,
        int $quantity = 1
    ): RedirectResponse|Response {
        if (! $this->isSupportedCurrency($plan)) {
            return Redirect::back()->with('error', __('messages.payment_gateway.currency_not_supported'));
        }

        if (! $this->isSupportedInterval($plan)) {
            return Redirect::back()->with('error', 'Interval value is not supported for this interval unit.');
        }

        try {
            if (is_null($this->getProduct())) {
                $this->createProduct();
            }

            $paypalPlan = $this->createPlan($plan, $taxes, $coupon);

            // Generate a unique ID
            $uniqueId = Str::uuid()->toString();

            // Store the data in cache
            // The data will be stored for 24 hours
            Cache::put($uniqueId, [
                'user_id' => Auth::id(),
                'plan_id' => $plan->id,
                'coupon_id' => $coupon?->id,
                'billing_info' => $billingInfo,
                'taxes' => $taxes->toArray()
            ], 24 * 60 * 60);

            $response = Http::withToken($this->accessToken)->asJson()->post("$this->url/v1/billing/subscriptions", [
                'plan_id' => $paypalPlan['id'],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'locale' => App::getLocale(),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'SUBSCRIBE_NOW',
                    'payment_method' => [
                        'payer_selected' => 'PAYPAL',
                        'payee_preferred' => 'IMMEDIATE_PAYMENT_REQUIRED',
                    ],
                    'return_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label()), 'success' => true]),
                    'cancel_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label()), 'success' => false]),
                ],
                'custom_id' => $uniqueId
            ])->throw();

            $body = $response->json();

            return Inertia::location($body['links'][0]['href']);
        } catch (RequestException|ConnectionException $e) {
            return Redirect::back()->with('error', $e->response->json('message') ?? $e->getMessage());
        }
    }

    private function isSupportedCurrency(Plan $plan): bool
    {
        return in_array($plan->currency->value, [...self::ZERO_DECIMAL_CURRENCIES, ...self::TWO_DECIMAL_CURRENCIES]);
    }

    private function isSupportedInterval(Plan $plan): bool
    {
        return $plan->interval_unit === IntervalUnit::Day && $plan->interval <= 365 || $plan->interval_unit === IntervalUnit::Week && $plan->interval <= 52 || $plan->interval_unit === IntervalUnit::Month && $plan->interval <= 12 || $plan->interval_unit === IntervalUnit::Year && $plan->interval <= 1;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function getProduct(): mixed
    {
        $response = Http::withToken($this->accessToken)
                        ->asJson()
                        ->get("$this->url/v1/catalogs/products/$this->productId")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function createProduct(): void
    {
        Http::withToken($this->accessToken)->asJson()->post("$this->url/v1/catalogs/products", [
            'id' => $this->productId,
            'name' => config('app.name'),
            'description' => 'Subscription to ' . config('app.name'),
            'type' => 'SERVICE',
            'category' => 'SOFTWARE',
        ])->throw();
    }

    // Maximum supported values:
    // DAY: 365
    // WEEK: 52
    // MONTH: 12
    // YEAR: 1
    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function createPlan(Plan $plan, Collection $taxes, ?Coupon $coupon): mixed
    {
        $amount = Calculate::amount($plan->price, 1, $taxes, $coupon?->discount);

        $response = Http::withToken($this->accessToken)->asJson()->post("$this->url/v1/billing/plans", [
            'product_id' => $this->productId,
            'name' => $plan->name,
            'description' => $plan->description,
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => Str::upper($plan->interval_unit->value),
                        'interval_count' => $plan->interval,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 0,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => in_array($plan->currency, self::ZERO_DECIMAL_CURRENCIES) ? round($amount) : round($amount, 2),
                            'currency_code' => $plan->currency,
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'payment_failure_threshold' => 0,
                'setup_fee' => [
                    'value' => 0,
                    'currency_code' => $plan->currency,
                ],
            ],
        ])->throw();

        return $response->json();
    }

    /**
     * @throws \Spatie\WebhookClient\Exceptions\InvalidConfig
     */
    public function handleWebhook(Request $request): Response
    {
        Log::debug($request->getContent());

        $webhookConfig = new WebhookConfig([
            'name' => 'paypal',
            'signature_validator' => PayPalSignatureValidator::class,
            'webhook_profile' => PayPalWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookRequest::class,
            'process_webhook_job' => ProcessPayPalWebhookJob::class
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $authAlgo = $request->header('PAYPAL-AUTH-ALGO');
        $certUrl = $request->header('PAYPAL-CERT-URL');

        $signature = $request->header('PAYPAL-TRANSMISSION-SIG');
        $transmissionId = $request->header('PAYPAL-TRANSMISSION-ID');
        $transmissionTime = $request->header('PAYPAL-TRANSMISSION-TIME');

        $response = Http::withToken($this->accessToken)
                        ->asJson()
                        ->post("$this->url/v1/notifications/verify-webhook-signature", [
                            'auth_algo' => $authAlgo,
                            'transmission_id' => $transmissionId,
                            'transmission_time' => $transmissionTime,
                            'cert_url' => $certUrl,
                            'webhook_id' => $this->webhookId,
                            'webhook_event' => json_decode($request->getContent()),
                            'transmission_sig' => $signature,
                        ]);

        $body = $response->json();

        if ($response->successful() && $body['verification_status'] === 'SUCCESS') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function cancelSubscription(string $id): void
    {
        Http::withToken($this->accessToken)->asJson()->post("$this->url/v1/billing/subscriptions/$id/cancel",
                [
                    'reason' => Auth::user()->is_admin ? 'Admin cancelled' : 'User cancelled'
                ])->throw();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function getSubscription($id): mixed
    {
        $response = Http::withToken($this->accessToken)
                        ->asJson()
                        ->get("$this->url/v1/billing/subscriptions/$id")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function getPlan($id): mixed
    {
        $response = Http::withToken($this->accessToken)
                        ->asJson()
                        ->get("$this->url/v1/billing/plans/$id")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json();
        }
        return null;
    }

    public function callback(Request $request): RedirectResponse
    {
        $response = Redirect::route('subscribe');
        if ($request->boolean('success')) {
            return $response->with('success', __('messages.payment.completed'));
        }

        return $response;
    }
}
