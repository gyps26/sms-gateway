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
use App\Helpers\Calculate;
use App\Jobs\ProcessStripeWebhookJob;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\WebhookRequest;
use App\SignatureValidators\StripeSignatureValidator;
use App\WebhookProfiles\StripeWebhookProfile;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Response;

class Stripe implements PaymentGateway
{
    const TWO_DECIMAL_CURRENCIES = [
        'AED', 'AFN', 'ALL', 'AMD', 'ANG', 'AOA', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BMD', 'BND',
        'BOB', 'BRL', 'BSD', 'BWP', 'BYN', 'BZD', 'CAD', 'CDF', 'CHF', 'CNY', 'COP', 'CRC', 'CVE', 'CZK', 'DKK', 'DOP',
        'DZD', 'EGP', 'ETB', 'EUR', 'FJD', 'FKP', 'GBP', 'GEL', 'GIP', 'GMD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HTG', 'HUF',
        'IDR', 'ILS', 'INR', 'JMD', 'KES', 'KGS', 'KHR', 'KYD', 'KZT', 'LAK', 'LBP', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL',
        'MKD', 'MMK', 'MNT', 'MOP', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD',
        'PAB', 'PEN', 'PGK', 'PHP', 'PKR', 'PLN', 'QAR', 'RON', 'RSD', 'RUB', 'SAR', 'SBD', 'SCR', 'SEK', 'SGD', 'SHP',
        'SLE', 'SOS', 'SRD', 'STD', 'SZL', 'THB', 'TJS', 'TOP', 'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'USD', 'UYU', 'UZS',
        'WST', 'XCD', 'YER', 'ZAR', 'ZMW',
    ];

    const ZERO_DECIMAL_CURRENCIES = [
        'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
    ];

    const SPECIAL_CURRENCIES = [
        'UGX', 'ISK'
    ];

    private string $secretKey;

    private StripeClient $client;

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.Stripe.secret_key');

        $this->client = new StripeClient($this->secretKey);
    }

    public static function label(): string
    {
        return 'Stripe';
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
            ],
        ];
    }

    public function initPayment(
        Plan $plan,
        Collection $taxes,
        ?Coupon $coupon,
        array $billingInfo,
        int $quantity = 1
    ): RedirectResponse|Response {
        try {
            if (! $this->isSupportedCurrency($plan)) {
                return Redirect::back()->with('error', __('messages.payment_gateway.currency_not_supported'));
            }

            $amount = Calculate::amount($plan->price, 1, $taxes, $coupon?->discount);

            if (in_array($plan->currency->value, self::ZERO_DECIMAL_CURRENCIES)) {
                $amount = round($amount);
            } else if (in_array($plan->currency->value, self::SPECIAL_CURRENCIES)) {
                $amount = round($amount) * 100;
            } else {
                $amount = round($amount, 2) * 100;
            }

            $customerId = Setting::retrieve('extras.stripe_customer_id', Auth::id());

            $customerDetails = [
                'email' => Auth::user()->email,
                'name' => trim($billingInfo['first_name'] . ' ' . $billingInfo['last_name']),
                'phone' => $billingInfo['phone'],
                'address' => [
                    'line1' => $billingInfo['address_line_1'],
                    'line2' => $billingInfo['address_line_2'],
                    'city' => $billingInfo['city'],
                    'postal_code' => $billingInfo['postal_code'],
                    'state' => $billingInfo['state'],
                    'country' => $billingInfo['country'],
                ],
                'metadata' => [
                    'id' => Auth::id(),
                ]
            ];

            if ($customerId) {
                $customer = $this->client->customers->update($customerId, $customerDetails);
            } else {
                $customer = $this->client->customers->create($customerDetails);
                Setting::store('extras.stripe_customer_id', $customer->id, Auth::id());
            }

            $session = $this->client->checkout->sessions->create([
                'customer' => $customer->id,
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => $plan->name,
                                'description' => $plan->description,
                            ],
                            'unit_amount' => $amount,
                            'currency' => $plan->currency->lowerCaseValue(),
                            'recurring' => [
                                'interval' => Str::lower($plan->interval_unit->value),
                                'interval_count' => $plan->interval,
                            ],
                            'tax_behavior' => 'exclusive',
                        ],
                        'quantity' => 1,
                    ]
                ],
                'mode' => 'subscription',
                'success_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label()), 'success' => true]),
                'cancel_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label()), 'success' => false]),
                'subscription_data' => [
                    'metadata' => [
                        'user_id' => Auth::id(),
                        'plan_id' => $plan->id,
                        'billing_info' => json_encode($billingInfo),
                        'taxes' => $taxes->toJson(),
                        'coupon_id' => $coupon?->id
                    ],
                ],
            ]);

            return Inertia::location($session->url);
        } catch (ApiErrorException $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    private function isSupportedCurrency(Plan $plan): bool
    {
        return in_array($plan->currency->value, [...self::ZERO_DECIMAL_CURRENCIES, ...self::TWO_DECIMAL_CURRENCIES, ...self::SPECIAL_CURRENCIES]);
    }

    /**
     * @throws \Spatie\WebhookClient\Exceptions\InvalidConfig
     */
    public function handleWebhook(Request $request): Response
    {
        Log::debug($request->getContent());

        $webhookConfig = new WebhookConfig([
            'name' => 'stripe',
            'signing_secret' => config('payment-gateways.Stripe.webhook_secret'),
            'signature_header_name' => 'Stripe-Signature',
            'signature_validator' => StripeSignatureValidator::class,
            'webhook_profile' => StripeWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookRequest::class,
            'process_webhook_job' => ProcessStripeWebhookJob::class
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

    /**
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function cancelSubscription(string $id): void
    {
        $this->client->subscriptions->cancel($id);
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
