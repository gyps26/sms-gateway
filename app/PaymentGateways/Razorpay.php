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

/** @noinspection PhpUndefinedFieldInspection */

namespace App\PaymentGateways;

use App\Contracts\PaymentGateway;
use App\Helpers\Calculate;
use App\Jobs\ProcessRazorpayWebhookJob;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\WebhookRequest;
use App\WebhookProfiles\RazorpayWebhookProfile;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Razorpay\Api\Api;
use Spatie\WebhookClient\SignatureValidator\DefaultSignatureValidator;
use Spatie\WebhookClient\WebhookConfig;
use Spatie\WebhookClient\WebhookProcessor;
use Spatie\WebhookClient\WebhookResponse\DefaultRespondsTo;
use Symfony\Component\HttpFoundation\Response;

class Razorpay implements PaymentGateway
{
    const ZERO_DECIMAL_CURRENCIES = [
        'BIF', 'CLP', 'DJF', 'GNF', 'ISK', 'JPY', 'KMF', 'KRW', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'
    ];

    const TWO_DECIMAL_CURRENCIES = [
        'AED', 'ALL', 'AMD', 'ARS', 'AUD', 'AWG', 'AZN', 'BAM', 'BBD', 'BDT', 'BGN', 'BMD', 'BND', 'BOB', 'BRL', 'BSD',
        'BTN', 'BWP', 'BZD', 'CAD', 'CHF', 'CNY', 'COP', 'CRC', 'CUP', 'CVE', 'CZK', 'DKK', 'DOP', 'DZD', 'EGP', 'ETB',
        'EUR', 'FJD', 'GBP', 'GHS', 'GIP', 'GMD', 'GTQ', 'GYD', 'HKD', 'HNL', 'HRK', 'HTG', 'HUF', 'IDR', 'ILS', 'INR',
        'JMD', 'KES', 'KGS', 'KHR', 'KYD', 'KZT', 'LAK', 'LKR', 'LRD', 'LSL', 'MAD', 'MDL', 'MGA', 'MKD', 'MMK', 'MNT',
        'MOP', 'MUR', 'MVR', 'MWK', 'MXN', 'MYR', 'MZN', 'NAD', 'NGN', 'NIO', 'NOK', 'NPR', 'NZD', 'PEN', 'PGK', 'PHP',
        'PKR', 'PLN', 'QAR', 'RON', 'RSD', 'RUB', 'SAR', 'SCR', 'SEK', 'SGD', 'SLL', 'SOS', 'SSP', 'SVC', 'SZL', 'THB',
        'TRY', 'TTD', 'TWD', 'TZS', 'UAH', 'USD', 'UYU', 'UZS', 'XCD', 'YER', 'ZAR', 'ZMW'
    ];

    const THREE_DECIMAL_CURRENCIES = [
        'BHD', 'IQD', 'JOD', 'KWD', 'OMR', 'TND'
    ];

    //https://razorpay.com/docs/payments/international-payments/#supported-currencies
    const RAZORPAY_PERIODS = [
        'Day' => 'daily',
        'Week' => 'weekly',
        'Month' => 'monthly',
        'Year' => 'yearly',
    ];
    const PERIOD_TO_DAYS = [
        'Day' => 1,
        'Week' => 7,
        'Month' => 30,
        'Year' => 365,
    ];
    private string $key;
    private string $secret;
    private Api $api;

    public function __construct()
    {
        $this->key = config('payment-gateways.Razorpay.key');
        $this->secret = config('payment-gateways.Razorpay.secret');

        $this->api = new Api($this->key, $this->secret);
    }

    public static function label(): string
    {
        return 'Razorpay';
    }

    public static function fields(): array
    {
        return [
            'key' => [
                'label' => 'Key',
                'type' => 'text'
            ],
            'secret' => [
                'label' => 'Secret',
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
    ): Response|RedirectResponse {
        try {
            if (! $this->isSupportedCurrency($plan)) {
                return Redirect::back()->with('error', __('messages.payment_gateway.currency_not_supported'));
            }

            // Generate a unique ID
            $cacheId = Str::uuid()->toString();

            // Store the data in cache
            // The data will be stored for 24 hours
            Cache::put($cacheId, [
                'user_id' => Auth::id(),
                'plan_id' => $plan->id,
                'coupon_id' => $coupon?->id,
                'billing_info' => $billingInfo,
                'taxes' => $taxes->toArray()
            ], 24 * 60 * 60);

            $razorpayPlan = $this->createPlan($plan, $taxes, $coupon);

            $totalCount = (int) ceil((5 * 365) / ($plan->interval * self::PERIOD_TO_DAYS[$plan->interval_unit->value]));

            $subscription = $this->api->subscription->create([
                'plan_id' => $razorpayPlan->id,
                'total_count' => $totalCount,
                'notes' => [
                    'cache_id' => $cacheId
                ],
            ]);

            return Inertia::location($subscription->short_url);
        } catch (Exception $e) {
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    private function createPlan(Plan $plan, Collection $taxes, ?Coupon $coupon)
    {
        $amount = Calculate::amount($plan->price, 1, $taxes, $coupon?->discount);

        if (in_array($plan->currency->value, self::TWO_DECIMAL_CURRENCIES)) {
            $amount = round($amount, 2) * 100;
        } else if (in_array($plan->currency->value, self::THREE_DECIMAL_CURRENCIES)) {
            $amount = round($amount, 3) * 1000;
        } else {
            $amount = round($amount);
        }

        return $this->api->plan->create([
            'period' => self::RAZORPAY_PERIODS[$plan->interval_unit->value],
            'interval' => $plan->interval,
            'item' => [
                'name' => $plan->name,
                'description' => $plan->description,
                'amount' => $amount,
                'currency' => $plan->currency->value,
            ],
        ]);
    }

    /**
     * @throws \Spatie\WebhookClient\Exceptions\InvalidConfig
     */
    public function handleWebhook(Request $request): Response
    {
        Log::debug($request->getContent());

        $webhookConfig = new WebhookConfig([
            'name' => 'stripe',
            'signing_secret' => config('payment-gateways.Razorpay.webhook_secret'),
            'signature_header_name' => 'X-Razorpay-Signature',
            'signature_validator' => DefaultSignatureValidator::class,
            'webhook_profile' => RazorpayWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookRequest::class,
            'process_webhook_job' => ProcessRazorpayWebhookJob::class
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

    private function isSupportedCurrency(Plan $plan): bool
    {
        return in_array($plan->currency->value, [...self::ZERO_DECIMAL_CURRENCIES, ...self::TWO_DECIMAL_CURRENCIES, ...self::THREE_DECIMAL_CURRENCIES]);
    }

    public function cancelSubscription(string $id): void
    {
        $this->api->subscription->fetch($id)->cancel([
            'cancel_at_cycle_end' => false,
        ]);
    }

    public function callback(Request $request): RedirectResponse
    {
        return Redirect::to('subscribe');
    }
}
