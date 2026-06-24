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

use App\Models\SenderId;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        SenderId::each(function (SenderId $senderId) {
            if ($senderId->sendingServer->user_id !== $senderId->user_id) {
                $senderId->users()->attach($senderId->sendingServer->user_id);
            }

            $senderId->users()->attach($senderId->user_id);
        });

        Schema::table('sender_ids', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sender_ids', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            SenderId::each(function (SenderId $senderId) {
                $user = $senderId->users()->first();
                if ($user) {
                    $senderId->user_id = $user->id;
                    $senderId->save();
                }
            });
        });
    }
};
