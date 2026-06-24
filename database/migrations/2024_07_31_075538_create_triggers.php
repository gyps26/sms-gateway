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

use App\Models\AutoResponse;
use App\Models\Campaign;
use App\Models\Device;
use App\Models\Message;
use App\Models\SenderId;
use App\Models\SendingServer;
use App\Models\Sim;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $prefix = DB::getTablePrefix();

        $device = Relation::getMorphAlias(Device::class);

        DB::unprepared("
            CREATE TRIGGER {$prefix}devices_ad_trigger
            AFTER DELETE
            ON {$prefix}devices
            FOR EACH ROW
            BEGIN
                DELETE FROM {$prefix}campaignables WHERE campaignable_id = OLD.id AND campaignable_type = '{$device}';
            END
        ");

        $sendingServer = Relation::getMorphAlias(SendingServer::class);

        DB::unprepared("
            CREATE TRIGGER {$prefix}sending_servers_ad_trigger
            AFTER DELETE
            ON {$prefix}sending_servers
            FOR EACH ROW
            BEGIN
                DELETE FROM {$prefix}campaignables WHERE campaignable_id = OLD.id AND campaignable_type = '{$sendingServer}';
                DELETE FROM {$prefix}quotas WHERE quotable_id = OLD.id AND quotable_type = '{$sendingServer}';
            END
        ");

        $sim = Relation::getMorphAlias(Sim::class);

        DB::unprepared("
            CREATE TRIGGER {$prefix}sims_ad_trigger
            AFTER DELETE
            ON {$prefix}sims
            FOR EACH ROW
            BEGIN
                DELETE FROM {$prefix}quotas WHERE quotable_id = OLD.id AND quotable_type = '$sim';
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = DB::getTablePrefix();

        DB::unprepared("DROP TRIGGER IF EXISTS {$prefix}devices_ad_trigger");
        DB::unprepared("DROP TRIGGER IF EXISTS {$prefix}sending_servers_ad_trigger");
        DB::unprepared("DROP TRIGGER IF EXISTS {$prefix}sims_ad_trigger");
    }
};
