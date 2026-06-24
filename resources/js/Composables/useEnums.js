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

import { useI18n } from '@/Composables/useI18n.js';

export function useEnums() {
    const { t } = useI18n();

    const callType = Object.freeze({
        'Outgoing': t('field.option.outgoing'),
        'Incoming': t('field.option.incoming'),
        'Missed': t('field.option.missed'),
        'Voicemail': t('field.option.voicemail'),
        'Rejected': t('field.option.rejected'),
        'Blocked': t('field.option.blocked'),
        'Answered Externally': t('field.option.answered_externally'),
    });

    const campaignStatus = Object.freeze({
        'Queued': t('field.option.queued'),
        'Processing': t('field.option.processing'),
        'Processed': t('field.option.processed'),
        'Scheduled': t('field.option.scheduled'),
        'Completed': t('field.option.completed'),
    });

    const campaignableStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Queued': t('field.option.queued'),
        'Stalled': t('field.option.stalled'),
        'Succeeded': t('field.option.succeeded'),
        'Failed': t('field.option.failed'),
        'Cancelling': t('field.option.cancelling'),
        'Cancelled': t('field.option.cancelled'),
    });

    const criterion = Object.freeze({
        'Match': t('field.option.match'),
        'Match Case': t('field.option.match_case'),
        'Contains': t('field.option.contains'),
        'Regex': t('field.option.regex'),
    });

    const daysOfWeek = Object.freeze({
        '1': t('field.option.monday'),
        '2': t('field.option.tuesday'),
        '3': t('field.option.wednesday'),
        '4': t('field.option.thursday'),
        '5': t('field.option.friday'),
        '6': t('field.option.saturday'),
        '7': t('field.option.sunday'),
    });

    const frequencyUnit = Object.freeze({
        'Minute': t('field.option.minute'),
        'Hour': t('field.option.hour'),
        'Day': t('field.option.day'),
        'Week': t('field.option.week'),
        'Month': t('field.option.month'),
        'Year': t('field.option.year'),
    });

    const intervalUnit = Object.freeze({
        'Day': t('field.option.day'),
        'Week': t('field.option.week'),
        'Month': t('field.option.month'),
        'Year': t('field.option.year'),
    });

    const messageStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Queued': t('field.option.queued'),
        'Processed': t('field.option.processed'),
        'Sent': t('field.option.sent'),
        'Failed': t('field.option.failed'),
        'Delivered': t('field.option.delivered'),
        'Received': t('field.option.received'),
    });

    const paymentStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Completed': t('field.option.completed'),
        'Refunded': t('field.option.refunded'),
        'Reversed': t('field.option.reversed'),
        'Failed': t('field.option.failed'),
        'Cancelled': t('field.option.cancelled'),
    });

    const signalStrength = Object.freeze({
        '4': t('field.option.great'),
        '3': t('field.option.good'),
        '2': t('field.option.moderate'),
        '1': t('field.option.poor'),
        '0': t('field.option.none_or_unknown'),
    });

    const subscriptionStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Active': t('field.option.active'),
        'Suspended': t('field.option.suspended'),
        'Cancelled': t('field.option.cancelled'),
        'Trial': t('field.option.trial'),
        'Expired': t('field.option.expired'),
    });

    const ussdPullStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Queued': t('field.option.queued'),
        'Completed': t('field.option.completed'),
        'Failed': t('field.option.failed'),
    });

    const webhookCallStatus = Object.freeze({
        'Pending': t('field.option.pending'),
        'Success': t('field.option.success'),
        'Failed': t('field.option.failed'),
        'Permanently Failed': t('field.option.permanently_failed'),
    });

    const webhookEvent = Object.freeze({
        'call.added': 'call.added',
        'message.received': 'message.received',
        'message.status.updated': 'message.status.updated',
        'ussd-pull.status.updated': 'ussd-pull.status.updated',
    });

    return {
        campaignStatus,
        campaignableStatus,
        callType,
        criterion,
        daysOfWeek,
        frequencyUnit,
        intervalUnit,
        messageStatus,
        paymentStatus,
        signalStrength,
        subscriptionStatus,
        ussdPullStatus,
        webhookCallStatus,
        webhookEvent,
    };
}
