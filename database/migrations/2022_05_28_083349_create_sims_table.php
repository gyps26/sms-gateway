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

use App\Models\Device;
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
        Schema::create('sims', function (Blueprint $table) {
            $table->id();
            $table->string('label')->nullable();
            $table->string('name')->nullable();
            $table->string('number')->nullable();
            $table->string('country')->nullable();
            $table->string('carrier')->nullable();
            $table->string('icc_id')->nullable();
            $table->string('mcc')->nullable();
            $table->string('mnc')->nullable();
            $table->boolean('data_roaming')->nullable();
            $table->integer('signal_strength')->nullable();
            $table->unsignedTinyInteger('slot');
            $table->boolean('active')->default(true);
            $table->foreignIdFor(Device::class)->constrained()->cascadeOnDelete();
            $table->unique(['device_id', 'slot']);
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
        Schema::dropIfExists('sims');
    }
};
