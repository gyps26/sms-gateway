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
import ComboboxInput from '@/Components/ComboboxInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DateRangeFilter from '@/Components/DateRangeFilter.vue';
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    ArrowPathIcon,
    DevicePhoneMobileIcon,
    EyeIcon,
    PencilSquareIcon,
    PresentationChartLineIcon,
    ServerStackIcon,
    TrashIcon,
    XCircleIcon
} from '@heroicons/vue/20/solid';
import { router, useForm } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';
import TextInput from "@/Components/TextInput.vue";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    campaigns: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const campaignBeingRetried = ref(null);
const campaignBeingCancelled = ref(null);
const campaignBeingUpdated = ref(null);

const updateCampaignForm = useForm({
    name: null,
});

const retryCampaignForm = useForm({});
const cancelCampaignForm = useForm({});

const daysOfWeek = useEnums().daysOfWeek;
const statusOptions = useEnums().campaignStatus;

const types = ['SMS', 'MMS', 'USSD Pull'];

const queryParams = reactive({
    'status': props.params.status,
    'type': props.params.type,
    'recurring': props.params.recurring,
    'after': props.params.after,
    'before': props.params.before,
});

const deleteCampaignsForm = useForm({
    ids: [],
    all: false,
    search: null,
});

const columns = [
    { name: t('field.name'), field: 'label', sortable: false },
    { name: t('field.status'), field: 'status', render: (status) => statusOptions[status] },
    { name: t('field.type'), field: 'type' },
    { name: t('field.timezone'), field: 'timezone' },
    { name: t('field.recurring'), field: 'recurring' },
    {
        name: t('field.frequency'),
        field: 'frequency',
        render: (frequency, campaign) => frequency ? `${ frequency } ${ campaign.frequency_unit }` : 'Not Available',
        sortable: false,
    },
    {
        name: t('field.ends_at'),
        field: 'ends_at',
        render: (endsAt) => endsAt ? new Date(endsAt).toLocaleString() : 'Not Available',
    },
    { name: t('field.active_hours'), field: 'active_hours', sortable: false },
    {
        name: t('field.days_of_week'),
        field: 'days_of_week',
        render: (data) => data.map(day => daysOfWeek[day]),
        sortable: false,
    },
    {
        name: t('field.scheduled_at'),
        field: 'scheduled_at',
        render: (scheduledAt) => scheduledAt ? new Date(scheduledAt).toLocaleString() : 'Not Scheduled',
    },
    { name: 'Created At', field: 'created_at', render: (createdAt) => new Date(createdAt).toLocaleString() },
];

const actions = [
    {
        name: t('page.dashboard'),
        icon: PresentationChartLineIcon,
        callback: (campaign) => router.visit(route('campaigns.dashboard', campaign)),
        screenReader: (campaign) => t('message.campaigns.action.dashboard', { campaign: campaign.label }),
    },
    {
        name: t('page.devices'),
        icon: DevicePhoneMobileIcon,
        callback: (campaign) => router.visit(route('campaigns.devices.index', campaign)),
        visible: (campaign) => campaign.devices_count > 0,
        screenReader: (campaign) => t('message.campaigns.action.devices', { campaign: campaign.label }),
    },
    {
        name: t('page.sending_servers'),
        icon: ServerStackIcon,
        callback: (campaign) => router.visit(route('campaigns.sending-servers.index', campaign)),
        visible: (campaign) => campaign.sending_servers_count > 0,
        screenReader: (campaign) => t('message.campaigns.action.sending_servers', { campaign: campaign.label }),
    },
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (campaign) => {
            campaignBeingUpdated.value = campaign;
            updateCampaignForm.name = campaign.name;
        },
        screenReader: (campaign) => t('message.campaigns.action.edit', { campaign: campaign.label }),
    },
    {
        name: t('action.retry'),
        icon: ArrowPathIcon,
        callback: (campaign) => campaignBeingRetried.value = campaign,
        screenReader: (campaign) => t('message.campaigns.action.retry', { campaign: campaign.label }),
    },
    {
        name: t('action.cancel'),
        icon: XCircleIcon,
        callback: (campaign) => campaignBeingCancelled.value = campaign,
        screenReader: (campaign) => t('message.campaigns.action.cancel', { campaign: campaign.label }),
    },
    {
        name: t('action.show'),
        icon: EyeIcon,
        callback: (campaign) => router.visit(route('campaigns.show', campaign)),
        screenReader: (campaign) => t('message.campaigns.action.show', { campaign: campaign.label }),
    },
];

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(deleteCampaignsForm, params),
        },
    ],
];

const cancelCampaign = () => {
    cancelCampaignForm.post(route('campaigns.cancel', campaignBeingCancelled.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => campaignBeingCancelled.value = null,
    });
};

const deleteCampaigns = () => {
    deleteCampaignsForm.post(route('campaigns.delete'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteCampaignsForm.reset(),
    });
};

const retryCampaign = () => {
    retryCampaignForm.post(route('campaigns.retry', campaignBeingRetried.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => campaignBeingRetried.value = null,
    });
};

const updateCampaign = () => {
    updateCampaignForm.put(route('campaigns.update', campaignBeingUpdated.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => campaignBeingUpdated.value = null,
    });
};

watch(queryParams, () => useQueryFilter(queryParams).refresh(['campaigns']));
</script>

<template>
    <ContentLayout :title="t('page.campaigns')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.campaigns') }}
                </h1>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <InputLabel :value="t('field.status')" for="status" />
                        <ComboboxInput id="status" v-model="queryParams.status"
                                       :hideSearchBox="true"
                                       :options="statusOptions"
                                       class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.type')" for="type" />
                        <ComboboxInput id="type" v-model="queryParams.type"
                                       :hideSearchBox="true"
                                       :options="types"
                                       class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.recurring')" for="recurring" />
                        <ComboboxInput id="recurring" v-model="queryParams.recurring"
                                       :hideSearchBox="true"
                                       :options="[{ label: t('action.yes'), value: 1 }, { label: t('action.no'), value: 0 }]"
                                       class="mt-1 block w-full" />
                    </div>
                    <DateRangeFilter v-model:after="queryParams.after" v-model:before="queryParams.before" />
                </div>
            </div>
        </div>

        <DataTable :bulk-actions="bulkActions" :actions="actions" :collection="campaigns" :columns="columns" :only="['campaigns']"
                   :params="params" />

        <!-- Edit Campaign Modal -->
        <DialogModal :show="campaignBeingUpdated !== null" @close="campaignBeingUpdated = null">
            <template #title>
                {{ t('message.campaigns.edit') }}
            </template>

            <template #content>
                <InputLabel :value="t('field.name')" for="name" />
                <TextInput id="name" v-model="updateCampaignForm.name" autofocus class="mt-1 block w-full"
                           type="text"></TextInput>
                <InputError :message="updateCampaignForm.errors.name" class="mt-2" />
            </template>

            <template #footer>
                <SecondaryButton @click="campaignBeingUpdated = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updateCampaignForm.processing }"
                    :disabled="updateCampaignForm.processing"
                    class="ml-3"
                    @click="updateCampaign"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Retry Campaign Confirmation Modal -->
        <ConfirmationModal :show="campaignBeingRetried != null" @close="campaignBeingRetried = null">
            <template #title>
                {{ t('message.campaigns.retry') }}
            </template>

            <template #content>
                {{ t('message.campaigns.retry_confirmation', { campaign: campaignBeingRetried.label }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="campaignBeingRetried = null">
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
        <ConfirmationModal :show="campaignBeingCancelled != null" @close="campaignBeingCancelled = null">
            <template #title>
                {{ t('message.campaigns.cancel') }}
            </template>

            <template #content>
                {{ t('message.campaigns.cancel_confirmation', { campaign: campaignBeingCancelled.label }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="campaignBeingCancelled = null">
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

        <!-- Delete Campaigns Confirmation Modal -->
        <ConfirmationModal :show="deleteCampaignsForm.ids.length > 0" @close="deleteCampaignsForm.reset()">
            <template #title>
                {{ t('message.campaigns.delete', deleteCampaignsForm.ids.length) }}
            </template>

            <template #content>
                {{ t('message.campaigns.delete_confirmation', deleteCampaignsForm.ids.length) }}
            </template>

            <template #footer>
                <SecondaryButton @click="deleteCampaignsForm.reset()">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteCampaignsForm.processing }"
                    :disabled="deleteCampaignsForm.processing"
                    class="ml-3"
                    @click="deleteCampaigns"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
