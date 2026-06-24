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
use App\Jobs\ProcessPaystackWebhookJob;
use App\Models\Coupon;
use App\Models\Plan;
use App\Models\WebhookRequest;
use App\SignatureValidators\PaystackSignatureValidator;
use App\WebhookProfiles\PaystackWebhookProfile;
use Exception;
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

class Paystack implements PaymentGateway
{
    const SUPPORTED_CURRENCIES = [
        'NGN', 'USD', 'GHS', 'ZAR', 'KES'
    ];

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('payment-gateways.Paystack.secret_key');
    }

    public static function label(): string
    {
        return 'Paystack';
    }

    public static function fields(): array
    {
        return [
            'secret_key' => [
                'label' => 'Secret Key',
                'type' => 'text'
            ]
        ];
    }

    /**
     * @throws \Exception
     */
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

            $amount = Calculate::amount($plan->price, 1, $taxes, $coupon?->discount);
            $amount = round($amount, 2) * 100;

            $response = Http::withToken($this->secretKey)->asJson()->post('https://api.paystack.co/plan', [
                'name' => $plan->name,
                'description' => json_encode([
                    'plan_id' => $plan->id,
                    'user_id' => Auth::id(),
                    'billing_info' => $billingInfo,
                    'coupon_id' => $coupon?->id,
                    'taxes' => $taxes->toArray()
                ]),
                'amount' => (int) $amount,
                'interval' => $this->getPaystackInterval($plan),
                'currency' => $plan->currency->value
            ])->throw();

            $paystackPlan = $response->json();

            $response = Http::withToken($this->secretKey)
                            ->asJson()
                            ->post('https://api.paystack.co/transaction/initialize', [
                                'email' => Auth::user()->email,
                                'amount' => $amount,
                                'currency' => $plan->currency->value,
                                'callback_url' => route('payment-gateways.callback', ['payment_gateway' => Str::slug(self::label())]),
                                'plan' => $paystackPlan['data']['plan_code']
                            ])
                            ->throw();

            $transaction = $response->json();

            return Inertia::location($transaction['data']['authorization_url']);
        } catch (RequestException|ConnectionException $e) {
            return Redirect::back()->with('error', $e->response->json('message') ?? $e->getMessage());
        }
    }

    private function isSupportedCurrency(Plan $plan): bool
    {
        return in_array($plan->currency->value, self::SUPPORTED_CURRENCIES);
    }

    /**
     * @throws \Exception
     */
    private function getPaystackInterval(Plan $plan): string
    {
        if ($plan->interval_unit === IntervalUnit::Month) {
            return match ($plan->interval) {
                1 => 'monthly',
                3 => 'quarterly',
                6 => 'biannually',
                default => throw new Exception('Interval value is not supported for this interval unit.')
            };
        } else {
            if ($plan->interval !== 1) {
                throw new Exception('Interval value is not supported for this interval unit.');
            }

            return match ($plan->interval_unit->value) {
                'Day' => 'daily',
                'Week' => 'weekly',
                'Year' => 'annually'
            };
        }
    }

    /**
     * @throws \Spatie\WebhookClient\Exceptions\InvalidConfig
     */
    public function handleWebhook(Request $request): Response
    {
        Log::debug($request->getContent());

        $webhookConfig = new WebhookConfig([
            'name' => 'stripe',
            'signing_secret' => config('payment-gateways.Paystack.secret_key'),
            'signature_header_name' => 'X-Paystack-Signature',
            'signature_validator' => PaystackSignatureValidator::class,
            'webhook_profile' => PaystackWebhookProfile::class,
            'webhook_response' => DefaultRespondsTo::class,
            'webhook_model' => WebhookRequest::class,
            'process_webhook_job' => ProcessPaystackWebhookJob::class
        ]);

        return (new WebhookProcessor($request, $webhookConfig))->process();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function cancelSubscription(string $id): void
    {
        $paystackSubscription = $this->getSubscription($id);

        if (is_null($paystackSubscription)) {
            return;
        }

        Http::withToken($this->secretKey)->asJson()->post("https://api.paystack.co/subscription/disable", [
            'code' => $id,
            'token' => $paystackSubscription['email_token']
        ])->throw();
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function getPlan(string $id)
    {
        $response = Http::withToken($this->secretKey)
                        ->get("https://api.paystack.co/plan/$id")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json('data');
        }
        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function getSubscription(string $id)
    {
        $response = Http::withToken($this->secretKey)
                        ->get("https://api.paystack.co/subscription/$id")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json('data');
        }
        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function listSubscriptions(int $planId)
    {
        $response = Http::withToken($this->secretKey)
                        ->get("https://api.paystack.co/subscription", [
                            'plan' => $planId
                        ])
                        ->throw();

        if ($response->successful()) {
            return $response->json('data');
        }
        return null;
    }

    /**
     * @throws \Illuminate\Http\Client\RequestException|\Illuminate\Http\Client\ConnectionException
     */
    public function getSubscriptionLink(string $id): ?string
    {
        $response = Http::withToken($this->secretKey)
                        ->get("https://api.paystack.co/subscription/$id/manage/link")
                        ->throwUnlessStatus(404);

        if ($response->successful()) {
            return $response->json('data.link');
        }
        return null;
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->input('reference')) {
            return Redirect::route('subscriptions.index')->with('success', __('messages.payment.completed'));
        }

        return Redirect::route('subscribe');
    }
}
