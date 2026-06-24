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

/**
 * Provides helper methods for byte conversions.
 */
class Bytes
{

    /**
     * The number of bytes in a kilobyte.
     *
     * @see http://wikipedia.org/wiki/Kilobyte
     */
    const KILOBYTE = 1024;

    /**
     * The allowed suffixes of a bytes string in lowercase.
     *
     * @see http://wikipedia.org/wiki/Kilobyte
     */
    const ALLOWED_SUFFIXES = [
        '',
        'b',
        'byte',
        'bytes',
        'k',
        'kb',
        'kilobyte',
        'kilobytes',
        'm',
        'mb',
        'megabyte',
        'megabytes',
        'g',
        'gb',
        'gigabyte',
        'gigabytes',
        't',
        'tb',
        'terabyte',
        'terabytes',
        'p',
        'pb',
        'petabyte',
        'petabytes',
        'e',
        'eb',
        'exabyte',
        'exabytes',
        'z',
        'zb',
        'zettabyte',
        'zettabytes',
        'y',
        'yb',
        'yottabyte',
        'yottabytes',
    ];

    /**
     * Parses a given byte size.
     *
     * @param  mixed  $size
     *   An integer or string size expressed as a number of bytes with optional SI
     *   or IEC binary unit prefix (e.g. 2, 3K, 5MB, 10G, 6GiB, 8 bytes, 9mbytes).
     *
     * @return int
     *   An integer representation of the size in bytes.
     *
     * @deprecated in drupal:9.1.0 and is removed from drupal:10.0.0. Use \Drupal\Component\Utility\Bytes::toNumber() instead
     *
     * @see https://www.drupal.org/node/3162663
     */
    public static function toInt(mixed $size): float|int
    {
        @trigger_error('\Drupal\Component\Utility\Bytes::toInt() is deprecated in drupal:9.1.0 and is removed from drupal:10.0.0. Use \Drupal\Component\Utility\Bytes::toNumber() instead. See https://www.drupal.org/node/3162663', E_USER_DEPRECATED);
        return self::toNumber($size);
    }

    /**
     * Parses a given byte size.
     *
     * @param  float|int|string  $size
     *   An integer, float, or string size expressed as a number of bytes with
     *   optional SI or IEC binary unit prefix (e.g. 2, 2.4, 3K, 5MB, 10G, 6GiB,
     *   8 bytes, 9mbytes).
     *
     * @return float
     *   The floating point value of the size in bytes.
     */
    public static function toNumber(float|int|string $size): float
    {
        // Remove the non-unit characters from the size.
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        // Remove the non-numeric characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size);
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power
            // of magnitude to multiply a kilobyte by.
            return round($size * pow(self::KILOBYTE, stripos('bkmgtpezy', $unit[0])));
        } else {
            // Ensure size is a proper number type.
            return round((float)$size);
        }
    }

    /**
     * Validate that a string is a representation of a number of bytes.
     *
     * @param  string  $string
     *   The string to validate.
     *
     * @return bool
     *   TRUE if the string is valid, FALSE otherwise.
     */
    public static function validate(string $string): bool
    {
        // Ensure that the string starts with a numeric character.
        if (!preg_match('/^[0-9]/', $string)) {
            return FALSE;
        }

        // Remove the numeric characters from the beginning of the value.
        $string = preg_replace('/^[0-9\.]+/', '', $string);

        // Remove remaining spaces from the value.
        $string = trim($string);

        return in_array(strtolower($string), self::ALLOWED_SUFFIXES);
    }

}
