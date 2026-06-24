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

namespace App\Helpers;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Collection;

class Timezone
{
    /**
     * @throws \Exception
     */
    public static function all(): array
    {
        $timezones = DateTimeZone::listIdentifiers();

        $timezone_offsets = [];
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        $timezone_list = new Collection();
        foreach ($timezone_offsets as $timezone => $offset) {
            $offset_prefix = $offset < 0 ? '-' : '+';
            $offset_formatted = gmdate('H:i', abs($offset));
            $parts = explode('/', $timezone);
            $pretty_name = implode(', ', array_reverse($parts));
            $pretty_name = str_replace(['_', 'St '], [' ', 'St. '], $pretty_name);
            $pretty_offset = "UTC {$offset_prefix}{$offset_formatted}";
            $timezone_list->add([
                'label' => "$pretty_name ({$pretty_offset})",
                'value' => $timezone
            ]);
        }

        return $timezone_list->sortBy('label')->values()->all();
    }
}
