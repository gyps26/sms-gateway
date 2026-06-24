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

export function useCalculator(amount, quantity, discount, taxes) {

    let grossTotal = quantity * amount;

    const inclusiveTaxRate = taxes.filter(tax => tax.inclusive).reduce((acc, tax) => acc + tax.rate, 0);

    grossTotal = (grossTotal * 100) / (100 + inclusiveTaxRate);

    discount = (grossTotal * discount) / 100;

    let netTotal = grossTotal - discount;

    taxes = taxes.map(tax => {
        tax.amount = (netTotal * tax.rate) / 100;
        return tax;
    });

    netTotal += taxes.reduce((acc, tax) => acc + tax.amount, 0);

    return { taxes, discount, grossTotal, netTotal };
}
