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

import { router, usePage } from '@inertiajs/vue3';
import { isEqual, isArray } from 'lodash';

export function useQueryFilter(params, defaults = {}) {
    const filter = (value, key) => {
        if (isArray(value) && isArray(defaults?.[key])) {
            return value.length > 0 && !isEqual(value, defaults[key]);
        }
        return value !== undefined && value !== null && value !== '' && !isEqual(value, defaults?.[key]);
    };

    const buildQueryString = () => {
        const searchParams = new URLSearchParams(window.location.search);
        Object.entries(params).forEach(([key, value]) => {
            if (isArray(value)) {
                let i = 0;
                while (searchParams.has(`${key}[${i}]`)) {
                    searchParams.delete(`${key}[${i}]`);
                    i++;
                }

                while (searchParams.has(`${key}[]`)) {
                    searchParams.delete(`${key}[]`);
                }
            } else {
                searchParams.delete(key)
            }

            if (filter(value, key)) {
                isArray(value)
                    ? value.forEach(val => searchParams.append(`${key}[]`, val))
                    : searchParams.append(key, value);
            }
        });
        return searchParams.toString();
    };

    const refresh = (only = []) => {
        let url = usePage().url.split('?')[0];
        const queryString = buildQueryString(params);
        router.get(`${url}?${queryString}`, {}, {
            preserveScroll: true,
            preserveState: true,
            replace: true,
            only: only,
        });
    };

    return { refresh };
}
