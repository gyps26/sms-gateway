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

import { usePage } from '@inertiajs/vue3';

export function useI18n(page = usePage()) {
    const messages = page.props.locale.messages;

    function t(key, params = {}) {
        if (typeof params === 'number') {
            params = { count: params };
        }

        let translation = getTranslation(key, messages);

        if (params.count !== undefined) {
            translation = getPluralizedTranslation(translation, params.count);
        }

        return replacePlaceholders(translation, params);
    }

    function getTranslation(key, messages) {
        const message = messages[key];
        if (message) {
            return message;
        }

        console.warn(`Missing translation for key "${ key }"`);
        return key;
    }

    function getPluralizedTranslation(translation, count) {
        const parts = translation.split('|');
        for (const part of parts) {
            const [range, text] = part.includes('}') ? part.split('}', 2) : [null, part];
            if (range) {
                const [min, max] = range.replace('{', '').replace('[', '').replace(']', '').split(',').map(Number);
                if ((min === 0 && count === 0) || (count >= min && (max === '*' || count <= max))) {
                    return text.trim();
                }
            } else {
                if (count === 1) return parts[0].trim();
                if (count > 1) return parts[1].trim();
            }
        }
        return translation;
    }

    function replacePlaceholders(translation, params) {
        return translation.replace(/:([a-zA-Z0-9_]+)/g, (_, k) => params[k] !== undefined ? params[k] : `:${ k }`);
    }

    return { t };
}
