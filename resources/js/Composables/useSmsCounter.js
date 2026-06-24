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

// noinspection SpellCheckingInspection

export function useSmsCounter() {
    const gsm7bitChars = '@£$¥èéùìòÇ\\nØø\\rÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ !\\"#¤%&\'()*+,-./0123456789:;<=>?¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà';
    const gsm7bitExChar = '\\^{}\\\\\\[~\\]|€';

    const gsm7bitRegExp = RegExp(`^[${gsm7bitChars}]*$`);
    const gsm7bitExRegExp = RegExp(`^[${gsm7bitChars}${gsm7bitExChar}]*$`);
    const gsm7bitExOnlyRegExp = RegExp(`^[\\${gsm7bitExChar}]*$`);

    const encodings = {
        GSM_7BIT: {
            name: 'GSM_7BIT', length: 160, multipartLength: 153,
        }, GSM_7BIT_EX: {
            name: 'GSM_7BIT_EX', length: 160, multipartLength: 153,
        }, UTF16: {
            name: 'UTF16', length: 70, multipartLength: 67,
        },
    };

    const count = (text) => {
        let encoding, length, messages, per_message, remaining;
        encoding = detectEncoding(text);
        length = text.length;
        if (encoding === encodings.GSM_7BIT_EX) {
            length += countGsm7bitEx(text);
        }
        per_message = encoding.length;
        if (length > per_message) {
            per_message = encoding.multipartLength;
        }
        messages = Math.ceil(length / per_message);
        remaining = (per_message * messages) - length;
        if (remaining === 0 && messages === 0) {
            remaining = per_message;
        }
        return {
            encoding: encoding.name, length: length, per_message: per_message, remaining: remaining, messages: messages,
        };
    };

    const detectEncoding = (text) => {
        switch (false) {
            case text.match(gsm7bitRegExp) == null:
                return encodings.GSM_7BIT;
            case text.match(gsm7bitExRegExp) == null:
                return encodings.GSM_7BIT_EX;
            default:
                return encodings.UTF16;
        }
    };

    const countGsm7bitEx = (text) => {
        let char2, chars;
        chars = (function () {
            let _i, _len, _results;
            _results = [];
            for (_i = 0, _len = text.length; _i < _len; _i++) {
                char2 = text[_i];
                if (char2.match(gsm7bitExOnlyRegExp) != null) {
                    _results.push(char2);
                }
            }
            return _results;
        }).call(this);
        return chars.length;
    };

    return { count };
}
