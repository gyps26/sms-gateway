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

use App\Http\Controllers\Api\v1\App\CallController as AppCallController;
use App\Http\Controllers\Api\v1\App\CampaignController as AppCampaignController;
use App\Http\Controllers\Api\v1\App\DeviceController as AppDeviceController;
use App\Http\Controllers\Api\v1\App\MessageController as AppMessageController;
use App\Http\Controllers\Api\v1\App\UpdateController as AppUpdateController;
use App\Http\Controllers\Api\v1\App\UssdPullController as AppUssdPullController;
use App\Http\Controllers\Api\v1\CallController;
use App\Http\Controllers\Api\v1\CampaignController;
use App\Http\Controllers\Api\v1\ContactController;
use App\Http\Controllers\Api\v1\ContactListController;
use App\Http\Controllers\Api\v1\DeviceController;
use App\Http\Controllers\Api\v1\FieldController;
use App\Http\Controllers\Api\v1\MessageController;
use App\Http\Controllers\Api\v1\SenderIdController;
use App\Http\Controllers\Api\v1\SendingServerController;
use App\Http\Controllers\Api\v1\UssdPullController;
use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\Api\v1')
    ->prefix('v1')
    ->as('api.v1.')
    ->middleware('installed')
    ->group(function () {
        // App routes
        Route::prefix('app')->as('app.')->group(function () {
            Route::post('devices/register', [AppDeviceController::class, 'register'])->name('devices.register');

            Route::middleware('auth:jwt')->group(function () {
                // Devices
                Route::post('devices/{device}/login', [AppDeviceController::class, 'login'])->name('devices.login');
                Route::post('devices/{device}/logout', [AppDeviceController::class, 'logout'])->name('devices.logout');
                Route::apiResource('devices', AppDeviceController::class)->only(['update']);

                Route::get('update', AppUpdateController::class)->name('update');

                Route::scopeBindings()->group(function () {
                    // Campaigns
                    Route::apiResource('devices.campaigns', AppCampaignController::class)
                         ->only(['index', 'show', 'update']);
                    Route::post('devices/{device}/campaigns/{campaign}/stop', [AppCampaignController::class, 'stop'])
                         ->name('devices.campaigns.stop');

                    // Messages
                    Route::apiResource('devices.messages', AppMessageController::class)->only(['store', 'update']);
                    Route::apiResource('devices.campaigns.messages', AppMessageController::class)
                         ->only(['index']);

                    // Calls
                    Route::apiResource('devices.calls', AppCallController::class)->only(['store']);

                    // Ussd Pulls
                    Route::apiResource('devices.ussd-pulls', AppUssdPullController::class)->only(['update']);
                    Route::apiResource('devices.campaigns.ussd-pulls', AppUssdPullController::class)
                         ->only(['index']);
                });
            });
        });

        // API routes
        Route::middleware('auth:sanctum')->group(function () {
            // Contact Lists
            Route::middleware('ability:view-contact-lists')
                 ->apiResource('contact-lists', ContactListController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:manage-contact-lists')
                 ->apiResource('contact-lists', ContactListController::class)
                 ->only(['store', 'update', 'destroy']);

            // Fields
            Route::middleware('ability:view-contact-lists')
                 ->apiResource('contact-lists.fields', FieldController::class)
                 ->only(['index', 'show'])
                 ->shallow();
            Route::middleware('ability:manage-contact-lists')
                 ->apiResource('contact-lists.fields', FieldController::class)
                 ->only(['store', 'update', 'destroy'])
                 ->shallow();

            // Calls
            Route::middleware('ability:view-calls')
                 ->apiResource('calls', CallController::class)
                 ->only(['index']);
            Route::middleware('ability:delete-calls')
                 ->apiResource('calls', CallController::class)
                 ->only(['destroy']);

            // Contacts
            Route::middleware('ability:view-contact-lists')
                 ->apiResource('contact-lists.contacts', ContactController::class)
                 ->only(['index', 'show'])
                 ->shallow();
            Route::middleware('ability:manage-contact-lists')
                 ->apiResource('contact-lists.contacts', ContactController::class)
                 ->only(['store', 'update', 'destroy'])
                 ->shallow();

            // Campaigns
            Route::middleware('ability:view-campaigns')
                 ->apiResource('campaigns', CampaignController::class)
                 ->only(['index', 'show']);
            Route::middleware('abilities:view-campaigns,view-devices')
                 ->get('campaigns/{campaign}/devices', [CampaignController::class, 'devices'])
                 ->name('campaigns.devices');
            Route::middleware('abilities:view-campaigns,view-sending-servers,view-sender-ids')
                 ->get('campaigns/{campaign}/sending-servers', [CampaignController::class, 'sendingServers'])
                 ->name('campaigns.sending-servers');
            Route::middleware('ability:manage-campaigns')
                 ->apiResource('campaigns', CampaignController::class)
                 ->only(['update', 'destroy']);

            // Devices
            Route::middleware('ability:view-devices')
                 ->apiResource('devices', DeviceController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:manage-devices')
                 ->apiResource('devices', DeviceController::class)
                 ->only(['update', 'destroy']);

            // Sending Servers
            Route::middleware('ability:view-sending-servers')
                 ->apiResource('sending-servers', SendingServerController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:manage-sending-servers')
                 ->apiResource('sending-servers', SendingServerController::class)
                 ->only(['store', 'update', 'destroy']);

            // Sender Ids
            Route::middleware('ability:view-sender-ids')
                 ->apiResource('sender-ids', SenderIdController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:manage-sender-ids')
                 ->apiResource('sending-servers.sender-ids', SenderIdController::class)
                 ->only(['store', 'destroy'])
                 ->shallow();

            // Messages
            Route::middleware('ability:send-messages')
                 ->match(['GET', 'POST'],'messages/send', [MessageController::class, 'send'])
                 ->name('messages.send');
            Route::middleware('ability:view-messages')
                 ->apiResource('messages', MessageController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:delete-messages')
                 ->apiResource('messages', MessageController::class)
                 ->only(['destroy']);

            // Ussd Pulls
            Route::middleware('ability:send-ussd-pulls')
                 ->match(['GET', 'POST'], 'ussd-pulls/send', [UssdPullController::class, 'send'])
                 ->name('ussd-pulls.send');
            Route::middleware('ability:view-ussd-pulls')
                 ->apiResource('ussd-pulls', UssdPullController::class)
                 ->only(['index', 'show']);
            Route::middleware('ability:delete-ussd-pulls')
                 ->apiResource('ussd-pulls', UssdPullController::class)
                 ->only(['destroy']);
        });
    });
