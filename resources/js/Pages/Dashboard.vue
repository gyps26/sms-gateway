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
import DialogModal from "@/Components/DialogModal.vue";
import LinkButton from "@/Components/LinkButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import StatsGrid from "@/Components/StatsGrid.vue";
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    ArrowPathIcon,
    ArrowPathRoundedSquareIcon,
    ArrowTopRightOnSquareIcon,
    BanknotesIcon,
    CheckBadgeIcon,
    CheckIcon,
    ClockIcon,
    CreditCardIcon,
    InboxArrowDownIcon,
    MegaphoneIcon,
    MicrophoneIcon,
    NoSymbolIcon,
    PhoneArrowDownLeftIcon,
    PhoneArrowUpRightIcon,
    PhoneIcon,
    PhoneXMarkIcon,
    RectangleStackIcon,
    UsersIcon,
    XMarkIcon
} from "@heroicons/vue/20/solid";
import { usePage, usePoll } from '@inertiajs/vue3';
import { get } from "lodash";
import { ref, watch } from 'vue';

const props = defineProps({
    announcement: {
        type: String,
        default: null,
    },
    counts: {
        type: Object,
        required: true,
    },
    credits: {
        type: Object,
        required: true,
    },
    subscription: {
        type: Object,
        default: null,
    },
    realtime: {
        type: Boolean,
        default: false,
    },
    welcome: {
        type: Boolean,
        default: false,
    }
});

const { t } = useI18n();
const page = usePage();

const showCreditsUsageSummary = ref(false);
const showWelcomeDialog = ref(props.welcome);

const {
    start,
    stop
} = usePoll(10000, { only: ['counts', 'subscription', 'realtime', 'announcement'] }, { autoStart: false });

const campaigns = [
    {
        label: t('field.option.queued'),
        color: 'bg-blue-500',
        value: get(props.counts, 'campaigns.queued', 0),
        route: route('campaigns.index', { status: 'Queued' }),
        icon: ClockIcon,
    },
    {
        label: t('field.option.processing'),
        color: 'bg-cyan-500',
        value: get(props.counts, 'campaigns.processing', 0),
        route: route('campaigns.index', { status: 'Processing' }),
        icon: ArrowPathIcon,
    },
    {
        label: t('field.option.processed'),
        color: 'bg-cyan-500',
        value: get(props.counts, 'campaigns.processed', 0),
        route: route('campaigns.index', { status: 'Processed' }),
        icon: ArrowPathRoundedSquareIcon,
    },
    {
        label: t('field.option.completed'),
        color: 'bg-green-500',
        value: get(props.counts, 'campaigns.completed', 0),
        route: route('campaigns.index', { status: 'Completed' }),
        icon: CheckBadgeIcon,
    },
];

const messages = [
    {
        label: t('field.option.received'),
        color: 'bg-blue-500',
        value: get(props.counts, 'messages.received', 0),
        route: route('messages.index', { statuses: ['Received'] }),
        icon: InboxArrowDownIcon,
    },
    {
        label: t('field.option.pending'),
        color: 'bg-yellow-500',
        value: get(props.counts, 'messages.pending', 0),
        route: route('messages.index', { statuses: ['Pending'] }),
        icon: ClockIcon,
    },
    {
        label: t('field.option.queued'),
        color: 'bg-blue-500',
        value: get(props.counts, 'messages.queued', 0),
        route: route('messages.index', { statuses: ['Queued'] }),
        icon: RectangleStackIcon,
    },
    {
        label: t('field.option.processed'),
        color: 'bg-cyan-500',
        value: get(props.counts, 'messages.processed', 0),
        route: route('messages.index', { statuses: ['Processed'] }),
        icon: ArrowPathRoundedSquareIcon,
    },
    {
        label: t('field.option.sent'),
        color: 'bg-green-500',
        value: get(props.counts, 'messages.sent', 0),
        route: route('messages.index', { statuses: ['Sent'] }),
        icon: CheckIcon
    },
    {
        label: t('field.option.delivered'),
        color: 'bg-green-500',
        value: get(props.counts, 'messages.delivered', 0),
        route: route('messages.index', { statuses: ['Delivered'] }),
        icon: CheckBadgeIcon,
    },
    {
        label: t('field.option.failed'),
        color: 'bg-red-500',
        value: get(props.counts, 'messages.failed', 0),
        route: route('messages.index', { statuses: ['Failed'] }),
        icon: XMarkIcon,
    },
];

const ussdPulls = [
    {
        label: t('field.option.pending'),
        color: 'bg-yellow-500',
        value: get(props.counts, 'ussd_pulls.pending', 0),
        route: route('ussd-pulls.index', { statuses: ['Pending'] }),
        icon: ClockIcon,
    },
    {
        label: t('field.option.queued'),
        color: 'bg-blue-500',
        value: get(props.counts, 'ussd_pulls.queued', 0),
        route: route('ussd-pulls.index', { statuses: ['Queued'] }),
        icon: RectangleStackIcon,
    },
    {
        label: t('field.option.completed'),
        color: 'bg-green-500',
        value: get(props.counts, 'ussd_pulls.completed', 0),
        route: route('ussd-pulls.index', { statuses: ['Completed'] }),
        icon: CheckBadgeIcon,
    },
    {
        label: t('field.option.failed'),
        color: 'bg-red-500',
        value: get(props.counts, 'ussd_pulls.failed', 0),
        route: route('ussd-pulls.index', { statuses: ['Failed'] }),
        icon: XMarkIcon,
    },
];

const calls = [
    {
        label: t('field.option.incoming'),
        color: 'bg-indigo-500',
        value: get(props.counts, 'calls.incoming', 0),
        route: route('calls.index', { type: 'Incoming' }),
        icon: PhoneArrowDownLeftIcon,
    },
    {
        label: t('field.option.outgoing'),
        color: 'bg-blue-500',
        value: get(props.counts, 'calls.outgoing', 0),
        route: route('calls.index', { type: 'Outgoing' }),
        icon: PhoneArrowUpRightIcon,
    },
    {
        label: t('field.option.missed'),
        color: 'bg-yellow-500',
        value: get(props.counts, 'calls.missed', 0),
        route: route('calls.index', { type: 'Missed' }),
        icon: PhoneIcon,
    },
    {
        label: t('field.option.voicemail'),
        color: 'bg-green-500',
        value: get(props.counts, 'calls.voicemail', 0),
        route: route('calls.index', { type: 'Voicemail' }),
        icon: MicrophoneIcon,
    },
    {
        label: t('field.option.rejected'),
        color: 'bg-red-500',
        value: get(props.counts, 'calls.rejected', 0),
        route: route('calls.index', { type: 'Rejected' }),
        icon: PhoneXMarkIcon,
    },
    {
        label: t('field.option.blocked'),
        color: 'bg-gray-500',
        value: get(props.counts, 'calls.blocked', 0),
        route: route('calls.index', { type: 'Blocked' }),
        icon: NoSymbolIcon,
    },
    {
        label: t('field.option.answered_externally'),
        color: 'bg-teal-500',
        value: get(props.counts, 'calls.answered_externally', 0),
        route: route('calls.index', { type: 'Answered Externally' }),
        icon: ArrowTopRightOnSquareIcon,
    },
];

const misc = [
    {
        label: t('field.credits'),
        color: 'bg-lime-500',
        value: page.props.auth.user.is_admin ? '∞' : (props.subscription ? (props.subscription.features.credits ?? '∞') : 0),
        callback: () => showCreditsUsageSummary.value = true,
        icon: CreditCardIcon,
    },
];

if (page.props.auth.user.is_admin) {
    misc.push({
        label: t('field.users'),
        color: 'bg-blue-500',
        value: get(props.counts, 'misc.users', 0),
        route: route('users.index'),
        icon: UsersIcon
    },
    {
        label: t('field.earnings'),
        color: 'bg-green-500',
        value: get(props.counts, 'misc.earnings', 0),
        route: route('payments.index'),
        icon: BanknotesIcon
    })
}

const items = {
    'campaigns': props.counts.campaigns ? campaigns : null,
    'messages': props.counts.messages ? messages : null,
    'ussd_pulls': props.counts.ussd_pulls ? ussdPulls : null,
    'calls': props.counts.calls ? calls : null,
    'misc': misc
};

watch(() => props.realtime, (realtime) => {
    if (realtime) {
        start();
    } else {
        stop();
    }
}, { immediate: true });
</script>

<template>
    <ContentLayout :title="$t('page.dashboard')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $t('page.dashboard') }}
                </h1>
            </div>
        </template>

        <div v-if="announcement" class="border-l-4 border-blue-400 bg-blue-50 p-4 m-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <MegaphoneIcon aria-hidden="true" class="size-8 text-blue-400" />
                </div>
                <div class="ml-3">
                    <div class="text-blue-700 space-y-4">
                        <p class="text-lg">{{ t('message.announcement') }}</p>
                        <div class="whitespace-pre-wrap">{{ announcement }}</div>
                    </div>
                </div>
            </div>
        </div>

        <template v-for="(stats, section) in items" :key="section">
            <StatsGrid v-if="stats" :stats="stats" :section="section" />
        </template>

        <DialogModal :show="showCreditsUsageSummary" @close="showCreditsUsageSummary = false">
            <template #title>
                {{ t('message.dashboard.credits_usage_summary') }}
            </template>

            <template #content>
                <div class="text-pretty leading-6">
                    <div class="mt-4">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                                    scope="col">
                                    {{ t('field.feature') }}
                                </th>
                                <th class="relative py-3.5 pr-4 pl-3 sm:pr-6" scope="col">
                                    {{ t('field.credits') }}
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="(value, feature) in credits" :key="feature">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                    {{ t(`field.${ feature }`) }}
                                    <template v-if="value.per_part">({{ t('field.per_part') }})</template>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    {{ value.amount }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showCreditsUsageSummary = false">
                    {{ t('action.close') }}
                </SecondaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showWelcomeDialog" @close="showWelcomeDialog = false">
            <template #title>
                {{ t('message.dashboard.welcome.title') }}
            </template>

            <template #content>
                <div class="text-pretty leading-6 space-y-4">
                    <p>{{ t('message.dashboard.welcome.description') }}</p>
                    <h3 class="text-lg font-semibold">{{ t('message.dashboard.welcome.messages.title') }}</h3>
                    <p>{{ t('message.dashboard.welcome.messages.description') }}</p>
                    <h3 class="text-lg font-semibold">{{ t('message.dashboard.welcome.ussd_pulls.title') }}</h3>
                    <p>{{ t('message.dashboard.welcome.ussd_pulls.description') }}</p>
                    <div>
                        <h3 class="text-lg font-semibold">{{ t('message.dashboard.welcome.send_android.title') }}</h3>
                        <p>{{ t('message.dashboard.welcome.send_android.description') }}</p>
                        <ul class="list-disc ml-8">
                            <li>
                                <span>{{ t('message.dashboard.welcome.send_android.step1') }}</span>
                                <div class="flex justify-center my-4">
                                    <LinkButton :href="route('devices.index')" target="_blank">
                                        <ArrowTopRightOnSquareIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                        {{ t('page.devices') }}
                                    </LinkButton>
                                </div>
                            </li>
                            <li>{{ t('message.dashboard.welcome.send_android.step2') }}</li>
                        </ul>
                    </div>
                    <div v-if="$page.props.auth.user.is_admin || (subscription ? (subscription.features.sender_ids ?? true) : false)">
                        <h3 class="text-lg font-semibold">{{ t('message.dashboard.welcome.send_custom_gateway.title') }}</h3>
                        <p>{{ t('message.dashboard.welcome.send_custom_gateway.description') }}</p>
                        <ul class="list-disc ml-8">
                            <li>{{ t('message.dashboard.welcome.send_custom_gateway.step1') }}</li>
                            <li>
                                <span>{{ t('message.dashboard.welcome.send_custom_gateway.step2') }}</span>
                                <div class="flex justify-center my-4">
                                    <LinkButton :href="route('sending-servers.index')" target="_blank">
                                        <ArrowTopRightOnSquareIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                        {{ t('page.sending_servers') }}
                                    </LinkButton>
                                </div>
                            </li>
                            <li>
                                <span>{{ t('message.dashboard.welcome.send_custom_gateway.step3') }}</span>
                                <div class="flex justify-center my-4">
                                    <LinkButton :href="route('sender-ids.index')" target="_blank">
                                        <ArrowTopRightOnSquareIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                        {{ t('page.sender_ids') }}
                                    </LinkButton>
                                </div>
                            </li>
                            <li>{{ t('message.dashboard.welcome.send_custom_gateway.step4') }}</li>
                        </ul>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="showWelcomeDialog = false">
                    {{ t('action.close') }}
                </SecondaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
