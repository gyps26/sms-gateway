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
    sendingServers: {
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

const sendingServerBeingRetried = ref(null);
const sendingServerBeingCancelled = ref(null);

const retryCampaignForm = useForm({});
const cancelCampaignForm = useForm({});

let columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('field.name'), field: 'name' },
    { name: t('field.option.pending'), field: 'pending_count' },
    { name: t('field.option.queued'), field: 'queued_count' },
    { name: t('field.option.failed'), field: 'failed_count' },
    { name: t('field.option.processed'), field: 'processed_count' },
    { name: t('field.option.sent'), field: 'sent_count' },
    { name: t('field.option.delivered'), field: 'delivered_count', visible: props.campaign.options.delivery_report },
    { name: t('field.status'), field: 'status', render: (status) => useEnums().campaignableStatus[status] },
    { name: t('field.resume_at'), field: 'resume_at' },
];

const actions = [
    {
        name: t('action.retry'),
        icon: ArrowPathIcon,
        callback: (sendingServer) => sendingServerBeingRetried.value = sendingServer,
        screenReader: (sendingServer) => t('message.campaign_sending_servers.action.retry', {
            campaign: props.campaign.label,
            sendingServer: sendingServer.name,
        }),
        visible: (sendingServer) => sendingServer.status === 'Cancelled' || sendingServer.status === 'Failed',
    },
    {
        name: t('action.cancel'),
        icon: XMarkIcon,
        callback: (sendingServer) => sendingServerBeingCancelled.value = sendingServer,
        screenReader: (sendingServer) => t('message.campaign_sending_servers.action.cancel', {
            campaign: props.campaign.label,
            sendingServer: sendingServer.name,
        }),
        visible: (sendingServer) => sendingServer.status === 'Pending' || sendingServer.status === 'Queued',
    },
];

const retryCampaign = () => {
    retryCampaignForm.post(route('campaigns.sending-servers.retry', [props.campaign, sendingServerBeingRetried.value]), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => sendingServerBeingRetried.value = null,
    });
};

const cancelCampaign = () => {
    cancelCampaignForm.post(route('campaigns.sending-servers.cancel', [props.campaign, sendingServerBeingCancelled.value]), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => sendingServerBeingCancelled.value = null,
    });
};
</script>

<template>
    <List :campaign="campaign" :campaign-status="campaignStatus" title="Overview">
        <DataTable :actions="actions" :collection="sendingServers" :columns="columns" :params="params" />
    </List>

    <!-- Retry Campaign Confirmation Modal -->
    <ConfirmationModal :show="sendingServerBeingRetried !== null" @close="sendingServerBeingRetried = null">
        <template #title>
            {{ t('message.campaign.retry') }}
        </template>

        <template #content>
            {{
                t('message.campaign_sending_servers.retry_confirmation', {
                    campaign: campaign.label,
                    sendingServer: sendingServerBeingRetried?.name,
                })
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="sendingServerBeingRetried = null">
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
    <ConfirmationModal :show="sendingServerBeingCancelled !== null" @close="sendingServerBeingCancelled = null">
        <template #title>
            {{ t('message.campaign.cancel') }}
        </template>

        <template #content>
            {{
                t('message.campaign_sending_servers.cancel_confirmation', {
                    campaign: campaign.label,
                    sendingServer: sendingServerBeingCancelled?.name,
                })
            }}
        </template>

        <template #footer>
            <SecondaryButton @click="sendingServerBeingCancelled = null">
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
