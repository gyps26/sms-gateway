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

use App\Http\Controllers\AutoResponseController;
use App\Http\Controllers\BlacklistController;
use App\Http\Controllers\CallController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactListController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\GeneralSettingsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MailSettingsController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessagingSettingsController;
use App\Http\Controllers\MiscellaneousSettingsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\PaymentGatewaySettingsController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\QuotaController;
use App\Http\Controllers\SaaSSettingsController;
use App\Http\Controllers\SenderIdController;
use App\Http\Controllers\SendingServerController;
use App\Http\Controllers\SimController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UssdPullController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\WebhookCallController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\InstallationGuard;
use Illuminate\Support\Facades\Route;

Route::get('language', LanguageController::class)->name('language');

Route::prefix('install')->middleware('not-installed')->group(function () {
    Route::get('/', [InstallController::class, 'show'])->name('install');

    Route::middleware(InstallationGuard::class)->group(function () {
        Route::get('requirements', [InstallController::class, 'requirements'])->name('install.requirements');
        Route::get('permissions', [InstallController::class, 'permissions'])->name('install.permissions');
        Route::get('database', [InstallController::class, 'database'])->name('install.database');
        Route::get('admin', [InstallController::class, 'admin'])->name('install.admin');
        Route::get('completed', [InstallController::class, 'completed'])->name('install.completed');
    });

    Route::post('database', [InstallController::class, 'storeConfig'])->name('install.config.store');
    Route::post('admin', [InstallController::class, 'storeAdmin'])->name('install.admin.store');
});

Route::middleware(['installed'])->group(function () {
    Route::get('/update', UpdateController::class)->name('update');

    Route::middleware(['updated'])->group(function () {
        Route::get('/', HomeController::class);

        Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)
             ->middleware(['signed', 'throttle:'.config('fortify.limiters.verification', '6,1')])
             ->name('verification.verify');

        Route::get('contact-lists/{contact_list}/contacts/unsubscribe', [ContactController::class, 'unsubscribe'])
             ->name('contacts.unsubscribe');

        Route::middleware([
            'auth:web',
            config('jetstream.auth_session'),
            'verified',
        ])->scopeBindings()->group(function () {
            Route::impersonate();

            Route::get('/dashboard', DashboardController::class)->name('dashboard');

            // Devices
            Route::resource('devices', DeviceController::class)->only(['index', 'update', 'destroy']);
            Route::post('devices/repair-pivot', [DeviceController::class, 'repairPivot'])->name('devices.repair-pivot');
            Route::post('devices/{device}/share', [DeviceController::class, 'share'])->name('devices.share');

            // QR Code
            Route::get('qr-code', [QrCodeController::class, 'create'])->name('qr-code');

            // Sims
            Route::resource('sims', SimController::class)->only(['index', 'update']);

            // Blacklist
            Route::resource('blacklist', BlacklistController::class)->only(['index', 'store']);
            Route::post('blacklist/delete', [BlacklistController::class, 'delete'])->name('blacklist.delete');

            // Campaigns
            Route::resource('campaigns', CampaignController::class)->only(['index', 'show', 'update']);
            Route::post('campaigns/delete', [CampaignController::class, 'delete'])->name('campaigns.delete');
            Route::post('campaigns/{campaign}/retry', [CampaignController::class, 'retry'])->name('campaigns.retry');
            Route::post('campaigns/{campaign}/cancel', [CampaignController::class, 'cancel'])->name('campaigns.cancel');
            Route::post('campaigns/{campaign}/devices/{device}/cancel', [DeviceController::class, 'cancelCampaign'])->name('campaigns.devices.cancel');
            Route::post('campaigns/{campaign}/sending-servers/{sending_server}/cancel', [SendingServerController::class, 'cancelCampaign'])->name('campaigns.sending-servers.cancel');
            Route::post('campaigns/{campaign}/devices/{device}/retry', [DeviceController::class, 'retryCampaign'])->name('campaigns.devices.retry');
            Route::post('campaigns/{campaign}/sending-servers/{sending_server}/retry', [SendingServerController::class, 'retryCampaign'])->name('campaigns.sending-servers.retry');
            Route::get('campaigns/{campaign}/import/log', [CampaignController::class, 'downloadImportLog'])
                 ->name('campaigns.import.log');
            Route::get('campaigns/{campaign}/devices', [CampaignController::class, 'devices'])->name('campaigns.devices.index');
            Route::get('campaigns/{campaign}/sending-servers', [CampaignController::class, 'sendingServers'])
                 ->name('campaigns.sending-servers.index');
            Route::get('campaigns/{campaign}/dashboard', [CampaignController::class, 'dashboard'])->name('campaigns.dashboard');
            Route::get('messages/create', [CampaignController::class, 'createMessages'])->name('messages.create');
            Route::post('messages/send', [CampaignController::class, 'sendMessages'])->name('messages.send');
            Route::get('ussd-pulls/create', [CampaignController::class, 'createUssdPulls'])->name('ussd-pulls.create');
            Route::post('ussd-pulls/send', [CampaignController::class, 'sendUssdPulls'])->name('ussd-pulls.send');

            // Messages
            Route::resource('messages', MessageController::class)->only(['index']);
            Route::post('messages/delete', [MessageController::class, 'delete'])->name('messages.delete');
            Route::post('messages/retry', [MessageController::class, 'retry'])->name('messages.retry');
            Route::post('messages/export/dispatch', [MessageController::class, 'dispatchExport'])
                 ->name('messages.export.dispatch');
            Route::get('messages/export/download', [MessageController::class, 'downloadExport'])
                 ->name('messages.export.download');

            // Calls
            Route::resource('calls', CallController::class)->only(['index']);
            Route::post('calls/delete', [CallController::class, 'delete'])->name('calls.delete');

            // Users
            Route::resource('users', UserController::class)->only(['index', 'store', 'destroy']);

            // Chat
            Route::get('chat', [ChatController::class, 'show'])->name('chat');
            Route::post('chat/send', [ChatController::class, 'send'])->name('chat.send');

            // Contact Lists
            Route::resource('contact-lists', ContactListController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

            // Contacts
            Route::resource('contact-lists.contacts', ContactController::class)->only(['index', 'store', 'update'])->shallow();
            Route::post('contact-lists/{contact_list}/contacts/delete', [ContactController::class, 'delete'])
                 ->name('contacts.delete');
            Route::get('contact-lists/{contact_list}/contacts/import', [ContactController::class, 'import'])
                 ->name('contacts.import');
            Route::post('contact-lists/{contact_list}/contacts/import/dispatch', [ContactController::class, 'dispatchImport'])
                 ->name('contacts.import.dispatch');
            Route::post('contact-lists/{contact_list}/contacts/import/cancel', [ContactController::class, 'cancelImport'])
                 ->name('contacts.import.cancel');
            Route::post('contact-lists/{contact_list}/contacts/import/clear', [ContactController::class, 'clearImport'])
                 ->name('contacts.import.clear');
            Route::get('contact-lists/{contact_list}/contacts/import/log', [ContactController::class, 'downloadImportLog'])
                 ->name('contacts.import.log');
            Route::get('contact-lists/{contact_list}/contacts/import.sample', [ContactController::class, 'downloadImportSample'])
                 ->name('contacts.import.sample');
            Route::get('contact-lists/{contact_list}/contacts/export', [ContactController::class, 'export'])
                 ->name('contacts.export');
            Route::post('contact-lists/{contact_list}/contacts/export/dispatch', [ContactController::class, 'dispatchExport'])
                 ->name('contacts.export.dispatch');
            Route::get('contact-lists/{contact_list}/contacts/export/download', [ContactController::class, 'downloadExport'])
                 ->name('contacts.export.download');
            Route::post('contact-lists/{contact_list}/contacts/export/clear', [ContactController::class, 'clearExport'])
                 ->name('contacts.export.clear');

            // Fields
            Route::resource('contact-lists.fields', FieldController::class)->only(['store', 'update', 'destroy'])->shallow();

            // Templates
            Route::resource('templates', TemplateController::class)->only(['index', 'store', 'update', 'destroy']);

            // Auto Responses
            Route::resource('auto-responses', AutoResponseController::class)->only(['index', 'store', 'update', 'destroy']);

            // Ussd Pulls
            Route::resource('ussd-pulls', UssdPullController::class)->only(['index']);
            Route::post('ussd-pulls/delete', [UssdPullController::class, 'delete'])->name('ussd-pulls.delete');
            Route::post('ussd-pulls/retry', [UssdPullController::class, 'retry'])->name('ussd-pulls.retry');

            // Sending Servers
            Route::resource('sending-servers', SendingServerController::class)->only(['index', 'store', 'update', 'destroy']);

            // Sender Ids
            Route::resource('sender-ids', SenderIdController::class)->only(['index', 'store', 'destroy']);
            Route::post('sender-ids/{sender_id}/share', [SenderIdController::class, 'share'])->name('sender-ids.share');

            // Webhooks
            Route::resource('webhooks', WebhookController::class)->only(['index', 'store', 'update', 'destroy']);

            // Webhook Calls
            Route::resource('webhook-calls', WebhookCallController::class)->only(['index']);
            Route::post('webhook-calls/{webhook_call}/retry', [WebhookCallController::class, 'retry'])
                 ->name('webhook-calls.retry');

            // Quotas
            Route::resource('quotas', QuotaController::class)->only(['update']);

            // General Settings
            Route::get('settings/general', [GeneralSettingsController::class, 'edit'])->name('settings.general.edit');
            Route::put('settings/general', [GeneralSettingsController::class, 'general'])->name('settings.general.update');
            Route::put('settings/general/registration', [GeneralSettingsController::class, 'registration'])
                 ->name('settings.general.registration.update');
            Route::put('settings/general/announcement', [GeneralSettingsController::class, 'announcement'])
                 ->name('settings.general.announcement.update');
            Route::put('settings/general/dashboard', [GeneralSettingsController::class, 'dashboard'])
                 ->name('settings.general.dashboard.update');
            Route::delete('logo', [GeneralSettingsController::class, 'destroyLogo'])->name('logo.destroy');

            // Mail Settings
            Route::get('settings/mail', [MailSettingsController::class, 'edit'])->name('settings.mail.edit');
            Route::put('settings/mail/mailer', [MailSettingsController::class, 'mailer'])
                 ->name('settings.mail.mailer.update');
            Route::post('settings/mail/mailer/test', [MailSettingsController::class, 'testMailer'])
                 ->name('settings.mail.mailer.test');
            Route::put('settings/mail/from', [MailSettingsController::class, 'fromAddress'])
                 ->name('settings.mail.from.update');
            Route::put('settings/mail/imap', [MailSettingsController::class, 'imap'])
                 ->name('settings.mail.imap.update');
            Route::post('settings/mail/imap/test', [MailSettingsController::class, 'testImap'])
                 ->name('settings.mail.imap.test');

            // Messaging Settings
            Route::get('settings/messaging', [MessagingSettingsController::class, 'edit'])
                 ->name('settings.messaging.edit');
            Route::put('settings/messaging/sim', [MessagingSettingsController::class, 'sim'])
                 ->name('settings.messaging.sim.update');
            Route::put('settings/messaging/auto-retry', [MessagingSettingsController::class, 'autoRetry'])
                 ->name('settings.messaging.auto-retry.update');
            Route::put('settings/messaging/prompts', [MessagingSettingsController::class, 'prompts'])
                 ->name('settings.messaging.prompts.update');
            Route::put('settings/messaging/phone-id', [MessagingSettingsController::class, 'phoneId'])
                 ->name('settings.messaging.phone-id.update');

            // Miscellaneous Settings
            Route::get('settings/misc', [MiscellaneousSettingsController::class, 'edit'])
                 ->name('settings.misc.edit');
            Route::put('settings/misc/firebase', [MiscellaneousSettingsController::class, 'firebase'])
                 ->name('settings.misc.firebase.update');
            Route::put('settings/misc/qr-code', [MiscellaneousSettingsController::class, 'qrCode'])
                 ->name('settings.misc.qr-code.update');
            Route::put('settings/misc/webhook', [MiscellaneousSettingsController::class, 'webhook'])
                 ->name('settings.misc.webhook.update');

            // Payment Gateway Settings
            Route::get('settings/payment-gateway', [PaymentGatewaySettingsController::class, 'edit'])
                 ->name('settings.payment-gateway.edit');
            Route::put('settings/payment-gateway/{payment_gateway}', [PaymentGatewaySettingsController::class, 'update'])
                 ->name('settings.payment-gateway.update');

            // SaaS Settings
            Route::get('settings/saas', [SaaSSettingsController::class, 'edit'])
                 ->name('settings.saas.edit');
            Route::put('settings/saas/billing-info', [SaaSSettingsController::class, 'billingInfo'])
                 ->name('settings.saas.billing-info.update');
            Route::put('settings/saas/credits', [SaaSSettingsController::class, 'credits'])
                 ->name('settings.saas.credits.update');
            Route::put('settings/saas/trial', [SaaSSettingsController::class, 'trial'])
                ->name('settings.saas.trial.update');

            // System Settings
            Route::get('settings/system', [SystemSettingsController::class, 'edit'])->name('settings.system.edit');
            Route::put('settings/system/update', [SystemSettingsController::class, 'update'])
                 ->name('settings.system.update');
            Route::put('settings/system/cron', [SystemSettingsController::class, 'cron'])
                 ->name('settings.system.cron.update');
            Route::put('settings/system/license', [SystemSettingsController::class, 'license'])
                 ->name('settings.system.license.update');

            // User Settings
            Route::get('user/settings', [UserSettingsController::class, 'edit'])->name('user.settings.edit');
            Route::put('user/settings/messaging/auto-retry', [UserSettingsController::class, 'autoRetry'])
                 ->name('user.settings.messaging.auto-retry.update');
            Route::put('user/settings/general/dashboard', [UserSettingsController::class, 'dashboard'])
                 ->name('user.settings.general.dashboard.update');
            Route::put('user/settings/mail/features', [UserSettingsController::class, 'features'])
                 ->name('user.settings.mail.features.update');
            Route::put('user/settings/misc/qr-code', [UserSettingsController::class, 'qrCode'])
                 ->name('user.settings.misc.qr-code.update');
            Route::put('user/settings/messaging/prompts', [UserSettingsController::class, 'prompts'])
                 ->name('user.settings.messaging.prompts.update');
            Route::put('user/settings/messaging/phone-id', [UserSettingsController::class, 'phoneId'])
                 ->name('user.settings.messaging.phone-id.update');
            Route::put('user/settings/messaging/sim', [UserSettingsController::class, 'sim'])
                 ->name('user.settings.messaging.sim.update');

            // Plans
            Route::resource('plans', PlanController::class)->only(['index', 'store', 'update']);

            // Taxes
            Route::resource('taxes', TaxController::class)->only(['index', 'store', 'update', 'destroy']);

            // Subscriptions
            Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'update']);
            Route::resource('plans.subscriptions', SubscriptionController::class)->only(['store']);
            Route::post('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])
                 ->name('subscriptions.cancel');
            Route::post('subscriptions/assign', [SubscriptionController::class, 'assign'])
                 ->name('subscriptions.assign');
            Route::get('plans/{plan}/subscriptions/checkout', [SubscriptionController::class, 'checkout'])
                 ->name('plans.subscriptions.checkout');
            Route::get('subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');

            // Payments
            Route::resource('payments', PaymentController::class)->only(['index', 'show']);
            Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
            Route::post('payments/{payment}/decline', [PaymentController::class, 'decline'])->name('payments.decline');
            Route::get('payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');

            // Coupons
            Route::resource('coupons', CouponController::class)->only(['index', 'store', 'update']);
            Route::post('coupons/apply', [CouponController::class, 'apply'])->name('coupons.apply');

            // Payment Gateways
            Route::get('payment-gateways/{payment_gateway}/callback', [PaymentGatewayController::class, 'callback'])
                 ->name('payment-gateways.callback');
        });

        Route::post('payment-gateways/{payment_gateway}/webhook', [PaymentGatewayController::class, 'webhook'])
             ->name('payment-gateways.webhook');

        Route::post('sending-servers/{sending_server}/webhook', [SendingServerController::class, 'webhook'])
             ->name('sending-servers.webhook');
    });
});
