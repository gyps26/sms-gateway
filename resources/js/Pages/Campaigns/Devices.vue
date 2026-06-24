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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DataTable from '@/Components/DataTable.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import List from '@/Pages/Campaigns/List.vue';
import { ArrowPathIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    devices: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
    campaignStatus: {
        type: Object,
        required: false,
    },
});

const { t } = useI18n();

const deviceBeingRetried = ref(null);
const deviceBeingCancelled = ref(null);

const cancelCampaignForm = useForm({});
const retryCampaignForm = useForm({});

let columns = [
    { name: t('field.model'), field: 'model' },
    { name: t('field.name'), field: 'name' },
    { name: t('field.option.pending'), field: 'pending_count' },
    { name: t('field.option.queued'), field: 'queued_count' },
    { name: t('field.option.failed'), field: 'failed_count' },
];

if (props.campaign.type === 'USSD Pull') {
    columns.push({ name: t('field.option.completed'), field: 'completed_count' });
} else {
    columns = columns.concat([
        { name: t('field.option.processed'), field: 'processed_count' },
        { name: t('field.option.sent'), field: 'sent_count' },
        {
            name: t('field.option.delivered'),
            field: 'delivered_count',
            visible: props.campaign.options.delivery_report,
        },
    ]);
}

columns.push({ name: t('field.status'), field: 'status', render: (status) => useEnums().campaignableStatus[status] });
columns.push({ name: t('field.resume_at'), field: 'resume_at' });

const actions = [
    {
        name: t('action.retry'),
        icon: ArrowPathIcon,
        callback: (device) => deviceBeingRetried.value = device,
        screenReader: (device) => t('message.campaign_devices.action.retry', {
            campaign: props.campaign.label,
            device: device.label,
        }),
        visible: (device) => device.status === 'Cancelled' || device.status === 'Failed',
    },
    {
        name: t('action.cancel'),
        icon: XMarkIcon,
        callback: (device) => deviceBeingCancelled.value = device,
        screenReader: (device) => t('message.campaign_devices.action.cancel', {
            campaign: props.campaign.label,
            device: device.label,
        }),
        visible: (device) => device.status === 'Pending' || device.status === 'Queued',
    },
];

const retryCampaign = () => {
    retryCampaignForm.post(route('campaigns.devices.retry', [props.campaign, deviceBeingRetried.value]), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => deviceBeingRetried.value = null,
    });
};

const cancelCampaign = () => {
    cancelCampaignForm.post(route('campaigns.devices.cancel', [props.campaign, deviceBeingCancelled.value]), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => deviceBeingCancelled.value = null,
    });
};
</script>

<template>
    <List :campaign="campaign" :campaign-status="campaignStatus" :title="$t('page.devices')">
        <DataTable :actions="actions" :collection="devices" :columns="columns" :params="params" />
    </List>

    <!-- Retry Campaign Confirmation Modal -->
    <ConfirmationModal :show="deviceBeingRetried !== null" @close="deviceBeingRetried = null">
        <template #title>
            {{ $t('message.campaigns.retry') }}
        </template>

        <template #content>
            {{
                $t('message.campaign_devices.retry_confirmation', {
                    campaign: props.campaign.label,
                    device: deviceBeingRetried.label,
                })
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="deviceBeingRetried = null">
                {{ t('action.no') }}
            </SecondaryButton>

            <PrimaryButton
                :class="{ 'opacity-25': retryCampaignForm.processing }"
                :disabled="retryCampaignForm.processing"
                class="ml-3"
                type="button"
                @click="retryCampaign"
            >
                {{ t('action.yes') }}
            </PrimaryButton>
        </template>
    </ConfirmationModal>

    <!-- Cancel Campaign Confirmation Modal -->
    <ConfirmationModal :show="deviceBeingCancelled !== null" @close="deviceBeingCancelled = null">
        <template #title>
            {{ $t('message.campaigns.cancel') }}
        </template>

        <template #content>
            {{
                $t('message.campaign_devices.cancel_confirmation', {
                    campaign: props.campaign.label,
                    device: deviceBeingCancelled.label,
                })
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="deviceBeingCancelled = null">
                {{ t('action.no') }}
            </SecondaryButton>

            <PrimaryButton
                :class="{ 'opacity-25': cancelCampaignForm.processing }"
                :disabled="cancelCampaignForm.processing"
                class="ml-3"
                type="button"
                @click="cancelCampaign"
            >
                {{ t('action.yes') }}
            </PrimaryButton>
        </template>
    </ConfirmationModal>
</template>
