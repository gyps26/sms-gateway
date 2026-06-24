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

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('timezone');
            $table->boolean('recurring')->default(false);
            $table->dateTime('repeat_at')->nullable();
            $table->integer('frequency')->nullable();
            $table->enum('frequency_unit', ['Minute', 'Hour', 'Day', 'Week', 'Month', 'Year'])->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->string('active_hours')->default('00:00-23:59');
            $table->json('days_of_week');
            $table->schemalessAttributes('payload');
            $table->schemalessAttributes('options');
            $table->enum('type', ['SMS', 'MMS', 'WhatsApp', 'USSD Pull']);
            $table->enum('status', ['Queued', 'Processing', 'Processed', 'Scheduled', 'Completed'])->default('Queued');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
