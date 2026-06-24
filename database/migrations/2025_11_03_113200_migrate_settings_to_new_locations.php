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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Migrate legacy settings to the new categorized settings.
     */
    public function up(): void
    {
        // Explicit mapping from old keys to new categorized keys
        $map = [
            // App
            'misc.features.registration' => 'app.registration',
            'misc.features.show_homepage' => 'app.homepage',

            // App: Dashboard
            'misc.dashboard.campaigns' => 'app.dashboard.stats.campaigns',
            'misc.dashboard.calls' => 'app.dashboard.stats.calls',
            'misc.dashboard.messages' => 'app.dashboard.stats.messages',
            'misc.dashboard.ussd_pulls' => 'app.dashboard.stats.ussd_pulls',
            'misc.features.realtime_dashboard' => 'app.dashboard.realtime',

            // Auth: QR code
            'misc.qr_code.lifespan' => 'auth.qr_code.lifespan',

            // Messaging: auto retry
            'misc.features.auto_retry' => 'messaging.auto_retry.enabled',
            'misc.auto_retry.max' => 'messaging.auto_retry.max_attempts',
            'misc.auto_retry.change_after' => 'messaging.auto_retry.change_after',

            // Messaging: prompts
            'misc.prompts.blacklist' => 'messaging.prompts.keywords.blacklist',
            'misc.prompts.whitelist' => 'messaging.prompts.keywords.whitelist',
            'misc.prompts.subscribe' => 'messaging.prompts.keywords.subscribe',
            'misc.prompts.unsubscribe' => 'messaging.prompts.keywords.unsubscribe',
            'misc.prompts.notify' => 'messaging.prompts.notify',

            // Messaging
            'misc.features.wait_for_confirmation' => 'messaging.wait_for_confirmation',
            'misc.features.email_to_message' => 'messaging.email_to_message',
            'misc.features.message_to_email' => 'messaging.message_to_email',

            // IMAP (Email)
            'misc.email_to_message.email' => 'imap.accounts.default.email',

            // Saas
            'app.billing_info' => 'saas.billing_info',

            // Saas: Credits
            'misc.credits.received.amount' => 'saas.credits.received.amount',
            'misc.credits.sms.amount' => 'saas.credits.sms.amount',
            'misc.credits.sms.per_part' => 'saas.credits.sms.per_part',
            'misc.credits.mms.amount' => 'saas.credits.mms.amount',
            'misc.credits.ussd_pull.amount' => 'saas.credits.ussd.amount',
            'misc.credits.call.amount' => 'saas.credits.call.amount',
            'misc.credits.webhook_call.amount' => 'saas.credits.webhook_call.amount',
            'misc.credits.message_to_email.amount' => 'saas.credits.message_to_email.amount',
            'misc.credits.email_to_message.amount' => 'saas.credits.email_to_message.amount',

            // Saas: Trial
            'misc.trial.plan_id' => 'saas.trial.plan_id',
            'misc.trial.days' => 'saas.trial.duration',
            'misc.trial.footer' => 'saas.trial.footer',

            // Payment Gateways
            'payment-gateways.bank-transfer.enabled' => 'payment_gateways.BankTransfer.enabled',
            'payment-gateways.bank-transfer.instructions' => 'payment_gateways.BankTransfer.instructions',
            'payment-gateways.cryptocom.enabled' => 'payment_gateways.CryptoCom.enabled',
            'payment-gateways.cryptocom.secret_key' => 'payment_gateways.CryptoCom.secret_key',
            'payment-gateways.cryptocom.webhook_secret' => 'payment_gateways.CryptoCom.webhook_secret',
            'payment-gateways.paypal.enabled' => 'payment_gateways.PayPal.enabled',
            'payment-gateways.paypal.sandbox' => 'payment_gateways.PayPal.sandbox',
            'payment-gateways.paypal.client_id' => 'payment_gateways.PayPal.client_id',
            'payment-gateways.paypal.client_secret' => 'payment_gateways.PayPal.client_secret',
            'payment-gateways.paypal.webhook_id' => 'payment_gateways.PayPal.webhook_id',
            'payment-gateways.paystack.enabled' => 'payment_gateways.Paystack.enabled',
            'payment-gateways.paystack.secret_key' => 'payment_gateways.Paystack.secret_key',
            'payment-gateways.paystack.webhook_secret' => 'payment_gateways.Paystack.webhook_secret',
            'payment-gateways.razorpay.enabled' => 'payment_gateways.Razorpay.enabled',
            'payment-gateways.razorpay.key' => 'payment_gateways.Razorpay.key',
            'payment-gateways.razorpay.secret' => 'payment_gateways.Razorpay.secret',
            'payment-gateways.razorpay.webhook_secret' => 'payment_gateways.Razorpay.webhook_secret',
            'payment-gateways.stripe.enabled' => 'payment_gateways.Stripe.enabled',
            'payment-gateways.stripe.secret_key' => 'payment_gateways.Stripe.secret_key',
            'payment-gateways.stripe.webhook_secret' => 'payment_gateways.Stripe.webhook_secret',
        ];

        if (DB::table('settings')->whereIn('name', array_keys($map))->doesntExist()) {
            // No legacy settings found; nothing to migrate
            return;
        }

        DB::transaction(function () use ($map) {
            foreach ($map as $from => $to) {
                DB::table('settings')->where('name', $from)->update(['name' => $to]);
            }
        });

        if (DB::table('settings')->where('name', 'app.support_url')->exists()) {
            return;
        }

        $admin = DB::table('users')->where('is_admin', true)->first();

        if ($admin) {
            DB::table('settings')
              ->insert(['name' => 'app.support_url', 'value' => "mailto:{$admin->email}"]);
        }

        Artisan::call('optimize:clear');
    }

    /**
     * Revert settings back to legacy keys where an unambiguous mapping exists.
     */
    public function down(): void
    {
        //
    }
};
