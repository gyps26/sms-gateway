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

import { groupBy, mapValues } from "lodash";

export function useGroupBy() {

    // https://gist.github.com/joyrexus/9837596
    const nest = function (seq, keys) {
        if (keys.length) {
            const first = keys[0];
            const rest = keys.slice(1);
            return mapValues(groupBy(seq, first), function (value) {
                return nest(value, rest);
            });
        } else {
            return seq;
        }
    };

    return { nest };
}