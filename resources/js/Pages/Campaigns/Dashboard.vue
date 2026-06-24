<!--
  - Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
  -
  - Author: Ravi Patel
  - Website: https://rbsoft.org/downloads/sms-gateway
  -
  - This software is licensed, not sold. Buyers are granted a limited, non-transferable license
  - to use this software exclusively on a single domain, subdomain, or computer. Usage on
  - multiple domains, subdomains, or computers requires the purchase of additional licenses.
  -
  - Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
  - is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
  - or creating derivative works based on this software is strictly prohibited.
  -
  - Unauthorized use, reproduction, or distribution of this software may result in severe civil
  - and criminal penalties and will be prosecuted to the fullest extent of the law.
  -
  - For licensing inquiries or support, please visit https://support.rbsoft.org.
  -->

<script setup>
import StatsGrid from "@/Components/StatsGrid.vue";
import { useI18n } from '@/Composables/useI18n.js';
import List from '@/Pages/Campaigns/List.vue';
import {
    ArrowPathRoundedSquareIcon,
    CheckBadgeIcon,
    CheckIcon,
    ClockIcon,
    RectangleStackIcon,
    XMarkIcon,
} from '@heroicons/vue/20/solid';
import { usePoll } from '@inertiajs/vue3';
import { get } from 'lodash';
import { watch } from 'vue';

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    counts: {
        type: Object,
        required: true,
    },
    realtime: {
        type: Boolean,
        default: false,
    },
    campaignStatus: {
        type: Object,
        required: false,
    },
});

const { t } = useI18n();

const { start, stop } = usePoll(10000, { only: ['counts', 'realtime'] }, { autoStart: false });

const common = [
    {
        label: t('field.option.pending'),
        color: 'bg-yellow-500',
        value: get(props.counts, 'pending', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Pending'] }),
        icon: ClockIcon,
    },
    {
        label: t('field.option.queued'),
        color: 'bg-blue-500',
        value: get(props.counts, 'queued', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Queued'] }),
        icon: RectangleStackIcon,
    },
    {
        label: t('field.option.failed'),
        color: 'bg-red-500',
        value: get(props.counts, 'failed', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Failed'] }),
        icon: XMarkIcon,
    },
];

const messages = [
    ...common,
    {
        label: t('field.option.processed'),
        color: 'bg-cyan-500',
        value: get(props.counts, 'processed', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Processed'] }),
        icon: ArrowPathRoundedSquareIcon,
    },
    {
        label: t('field.option.sent'),
        color: 'bg-green-500',
        value: get(props.counts, 'sent', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Sent'] }),
        icon: CheckIcon
    },
    {
        label: t('field.option.delivered'),
        color: 'bg-green-500',
        value: get(props.counts, 'delivered', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Delivered'] }),
        visible: props.campaign.options.delivery_report ? true : false,
        icon: CheckBadgeIcon,
    },
];

const ussdPulls = [
    ...common,
    {
        label: t('field.option.completed'),
        color: 'bg-purple-500',
        value: get(props.counts, 'completed', 0),
        route: route('campaigns.show', { campaign: props.campaign.id, statuses: ['Completed'] }),
        icon: RectangleStackIcon,
    },
]

const stats = props.campaign.type === 'USSD Pull' ? ussdPulls : messages;

watch(() => props.realtime, (realtime) => {
    if (props.campaign.status !== 'Processed') return;

    if (realtime) {
        start();
    } else {
        stop();
    }
}, { immediate: true });
</script>

<template>
    <List :campaign="campaign" :campaign-status="campaignStatus" :title="t('page.dashboard')">
        <StatsGrid :stats="stats" />
    </List>
</template>
