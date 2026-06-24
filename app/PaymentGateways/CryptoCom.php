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
use App\Jobs\ProcessCryptoComWebhookJob;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\WebhookRequest;
use App\SignatureValidators\CryptoComSignatureValidator;
use App\WebhookProfiles\CryptoComWebhookProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo;
use Symfony\Component\HttpFoundation\Response;

class CryptoCom implements PaymentGateway
{
    // https://pay-docs.crypto.com/#api-reference-resources-payments-pricing-currencies
    const TWO_DECIMAL_CURRENCIES = [
        'USD', 'EUR', 'AUD', 'CAD', 'GBP', 'BGN', 'BRL', 'CHF', 'CNY', 'COP', 'DKK', 'HKD', 'IDR', 'INR', 'MDL', 'MOP',
        'MXN', 'MYR', 'NOK', 'PHP', 'RON', 'RUB', 'SAR', 'SEK', 'SGD', 'THB', 'TRY', 'TWD', 'UAH', 'ZAR'
    ];

    const ZERO_DECIMAL_CURRENCIES = [
        'CLP', 'JPY', 'KRW'
    ];

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.CryptoCom.secret_key');
    }

    public static function label(): string
    {
        return 'Crypto.com';
    }

    public static function fields(): array
    {
        return [
            'secret_key' => [
                'label' => 'Secret Key',
                'type' => 'text'
            ],
            'webhook_secret' => [
                'label' => 'Webhook Secret',
                'type' => 'text'
            ]
        ];
    }

    public function initPayment(
        Plan $plan,
        Collection $taxes,
        ?Coupon $coupon,
        array $billingInfo,
        int $quantity = 1
    ): Response|RedirectResponse {
        if ($plan->interval_unit !== IntervalUnit::Month) {
            return Redirect::back()->with('error', __('messages.payment_gateway.crypto_com.interval_not_supported'));
        }

        if (! $this->isSupportedCurrency($plan)) {
            return Redirect::back()->with('error', __('messages.payment_gateway.currency_not_supported'));
        }

        try {
            $cryptoComProduct = $this->createProduct($plan, $taxes, $coupon);
            $cryptoComCustomer = $this->createCustomer();

            $response = Http::withBasicAuth($this->secretKey, '')
                            ->asJson()
                            ->post('https://pay.crypto.com/api/subscriptions', [
                                'customer_id' => $cryptoComCustomer['id'],
                                'return_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label())]),
                                'billing_cycle_anchor' => now()->timestamp,
                                'metadata' => [
                                    'plan_id' => $plan->id,
                                    'user_id' => Auth::id(),
                                    'taxes' => $taxes->toArray(),
                                    'billing_info' => $billingInfo,
                                    'coupon_id' => $coupon?->id
                                ],
                                'items' => [
                                    [
                                        'plan_id' => $cryptoComProduct['pricing_plans'][0]['id'],
                                        'quantity' => 1,
                                    ]
                                ]
                            ])
                            ->throw();

            $body = $response->json();

            return Inertia::location($body['subscription_url']);
        } catch (RequestException|ConnectionException $e) {
            return Redirect::back()->with('error', $e->response->json('error.error_message') ?? $e->getMessage());
        }
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function createProduct(Plan $plan, Collection $taxes, ?Coupon $coupon)
    {
        $amount = Calculate::amount($plan->price, 1, $taxes, $coupon?->discount);

        $response = Http::withBasicAuth($this->secretKey, '')->asJson()->post('https://pay.crypto.com/api/products', [
            'name' => config('app.name'),
            'active' => true,
            'description' => config('app.name') . ' subscription',
            'pricing_plans' => [
                [
                    'amount' => in_array($plan->currency->value, self::ZERO_DECIMAL_CURRENCIES) ? round($amount) : round($amount, 2) * 100,
                    'currency' => $plan->currency->value,
                    'active' => true,
                    'description' => $plan->description,
                    'interval' => 'month',
                    'interval_count' => $plan->interval,
                    'purchase_type' => 'recurring'
                ]
            ]
        ])->throw();

        return $response->json();
    }

    private function isSupportedCurrency(Plan $plan): bool
    {
        return in_array($plan->currency->value, [...self::ZERO_DECIMAL_CURRENCIES, ...self::TWO_DECIMAL_CURRENCIES]);
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    private function createCustomer()
    {
        $response = Http::withBasicAuth($this->secretKey, '')->asJson()->post('https://pay.crypto.com/api/customers', [
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
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
            'name' => 'crypto_com',
            'signing_secret' => config('payment-gateways.CryptoCom.webhook_secret'),
            'signature_header_name' => 'Pay-Signature',
            'signature_validator' => CryptoComSignatureValidator::class,
            'webhook_profile' => CryptoComWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookRequest::class,
            'process_webhook_job' => ProcessCryptoComWebhookJob::class
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function cancelSubscription(string $id): void
    {
        Http::withBasicAuth($this->secretKey, '')
            ->asJson()
            ->post("https://pay.crypto.com/api/subscriptions/$id/cancel")
            ->throw();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function getSubscription(string $id)
    {
        $response = Http::withBasicAuth($this->secretKey, '')
                        ->asJson()
                        ->get("https://pay.crypto.com/api/subscriptions/$id")
                        ->throw();

        return $response->json();
    }

    public function callback(Request $request): RedirectResponse
    {
        return Redirect::route('subscribe')->with('success', __('messages.payment.completed'));
    }
}
