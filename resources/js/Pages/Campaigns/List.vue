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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Tabs from '@/Components/Tabs.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    ArrowDownTrayIcon,
    ArrowPathIcon,
    DevicePhoneMobileIcon,
    EyeIcon,
    PresentationChartLineIcon,
    ServerStackIcon,
} from '@heroicons/vue/20/solid';
import { router, usePage, usePoll } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
    campaignStatus: {
        type: Object,
        required: false,
    },
});

const { t } = useI18n();

const tabs = [
    {
        name: t('page.dashboard'),
        icon: PresentationChartLineIcon,
        href: route('campaigns.dashboard', props.campaign),
        current: route().current('campaigns.dashboard', props.campaign),
    },
    {
        name: t('page.devices'),
        icon: DevicePhoneMobileIcon,
        href: route('campaigns.devices.index', props.campaign),
        current: route().current('campaigns.devices.index', props.campaign),
        visible: props.campaign.devices_count > 0,
    },
    {
        name: t('page.sending_servers'),
        icon: ServerStackIcon,
        href: route('campaigns.sending-servers.index', props.campaign),
        current: route().current('campaigns.sending-servers.index', props.campaign),
        visible: props.campaign.sending_servers_count > 0,
    },
    {
        name: t('action.show'),
        icon: EyeIcon,
        href: route('campaigns.show', props.campaign),
        current: route().current('campaigns.show', props.campaign),
    },
];

const { stop } = usePoll(2000, { only: ['campaignStatus'] }, {
    autoStart: props.campaign.status !== 'Processed',
});

const downloadImportLog = () => {
    window.location = route('campaigns.import.log', props.campaign);
};

watch(() => props.campaignStatus, (campaignStatus) => {
    if (campaignStatus.progress === null || campaignStatus.progress === 100) {
        stop();
    }
});
</script>

<template>
    <ContentLayout :title="`${campaign.label}:${title}`">
        <template #header>
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ campaign.label }}
                    </h1>
                </div>
            </div>
        </template>

        <template v-if="campaign.status !== 'Queued' && campaign.status !== 'Processing'">
            <div class="px-4 sm:px-6 lg:mx-auto lg:px-8">
                <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                    <div class="md:flex md:items-center md:justify-between">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
                        <div class="mt-3 flex md:absolute md:top-3 md:right-0 md:mt-0">
                            <slot name="actions" />
                        </div>
                    </div>
                    <div class="mt-4">
                        <Tabs :tabs="tabs"></Tabs>
                    </div>
                </div>
            </div>

            <div class="mt-8">
                <slot />
            </div>
        </template>

        <div v-else class="px-4 sm:px-6 lg:mx-auto lg:px-8">
            <div class="text-base">
                <span v-if="campaignStatus.progress !== 100">
                    {{ t('message.job.running_in_background', { job: t('entity.campaign') }) }}<br>
                    {{ t('message.job.navigate_away_or_close') }}
                </span>
                <span v-else>
                    {{ t('message.job.processed', { job: t('entity.campaign') }) }}
                </span>
            </div>
            <div class="mt-6">
                <div class="w-full bg-gray-200 rounded-sm">
                    <ProgressBar :progress="campaignStatus.progress"></ProgressBar>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                    <span v-if="campaignStatus.cancelled">
                        {{ t('message.job.being_cancelled', { job: t('entity.campaign') }) }}
                    </span>
                    <span v-else-if="campaignStatus.processed > 0">
                        {{
                            t('message.job.progress', {
                                total: campaignStatus.total,
                                processed: campaignStatus.processed,
                                failures: campaignStatus.failures,
                            })
                        }}
                    </span>
                    <span v-else>{{ t('message.job.queued', { job: t('entity.campaign') }) }}</span>
                </div>
                <div v-if="campaignStatus.progress === 100" class="mt-6 space-x-4">
                    <PrimaryButton v-if="campaign.payload.spreadsheet" @click="downloadImportLog">
                        <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ t('action.download_log') }}
                    </PrimaryButton>
                    <SecondaryButton @click="router.visit(usePage().url)">
                        <ArrowPathIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ t('action.refresh') }}
                    </SecondaryButton>
                </div>
            </div>
        </div>
    </ContentLayout>
</template>
