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
import { ArrowPathIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, reactive, watch } from 'vue';

const props = defineProps({
    ussdPulls: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    campaign: {
        type: Object,
        default: null,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const defaults = {
    user: usePage().props.auth.user.is_admin ? props.campaign?.user_id : usePage().props.auth.user.id,
    campaign: props.campaign?.id,
    sort_by: 'sent_at',
};

const queryParams = reactive({
    campaign: defaults.campaign ?? props.params.campaign,
    user: defaults.user ?? props.params.user,
    sim: props.params.sim,
    statuses: props.params.statuses,
    after: props.params.after,
    before: props.params.before,
});

const deleteUssdPullsForm = useForm({
    ids: [],
    all: false,
    search: null,
    ...queryParams,
});

const retryUssdPullsForm = useForm({
    ids: [],
    all: false,
    search: null,
    ...queryParams,
});

const deleteUssdPulls = () => {
    deleteUssdPullsForm.post(route('ussd-pulls.delete'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteUssdPullsForm.reset(),
    });
};

const retryUssdPulls = () => {
    retryUssdPullsForm.post(route('ussd-pulls.retry'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => retryUssdPullsForm.reset(),
    });
};

const statusOptions = useEnums().ussdPullStatus;

const columns = [
    { name: t('field.code'), field: 'code' },
    { name: t('field.response'), field: 'response', sortable: false },
    {
        name: t('field.received_at'),
        field: 'received_at',
        render: (receivedAt) => receivedAt ? new Date(receivedAt).toLocaleString() : null,
    },
    { name: t('field.sent_at'), field: 'sent_at', render: (sentAt) => new Date(sentAt).toLocaleString() },
    {
        name: t('entity.sim'),
        field: 'sim_id',
        render: (simId, ussdPull) => props.users[ussdPull.user_id].sims[simId].label,
        sortable: false,
    },
    { name: t('field.status'), field: 'status', render: (status) => statusOptions[status] },
];

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(deleteUssdPullsForm, params, queryParams),
        },
        {
            name: t('action.retry'),
            icon: ArrowPathIcon,
            callback: (params) => Object.assign(retryUssdPullsForm, params, queryParams),
        }
    ],
];

const simOptions = computed(() => {
    if (queryParams.user && props.users[queryParams.user]) {
        return Object.values(props.users[queryParams.user].sims).map(function (sim) {
            return { value: sim.id, label: sim.label };
        });
    }
});

watch(() => queryParams.user, (value, oldValue) => {
    if (value !== oldValue) {
        queryParams.sim = null;
    }
});

watch(queryParams, () => useQueryFilter(queryParams, defaults).refresh(['ussdPulls']));
</script>

<template>
    <div class="bg-white rounded-lg shadow m-8">
        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-if="$page.props.auth.user.is_admin && campaign === null">
                    <InputLabel :value="t('entity.user')" for="user" />
                    <ComboboxInput id="user" v-model="queryParams.user"
                                   :options="Object.values(users)"
                                   class="mt-1 block w-full"
                                   value-attribute="id" />
                </div>
                <div>
                    <InputLabel :value="t('entity.sim')" for="sim" />
                    <ComboboxInput id="sim" v-model="queryParams.sim"
                                   :options="simOptions"
                                   class="mt-1 block w-full" />
                </div>
                <div>
                    <InputLabel :value="t('field.statuses')" for="statuses" />
                    <ComboboxInput id="statuses" v-model="queryParams.statuses"
                                   :hideSearchBox="true"
                                   :options="statusOptions"
                                   class="mt-1 block w-full"
                                   multiple />
                </div>
                <DateRangeFilter v-model:after="queryParams.after"
                                 v-model:before="queryParams.before" />
            </div>
        </div>
    </div>

    <DataTable :bulk-actions="bulkActions" :collection="ussdPulls" :columns="columns" :defaults="defaults"
               :only="['ussdPulls']" :params="params" />

    <!-- Delete USSD Pulls Confirmation Modal -->
    <ConfirmationModal :show="deleteUssdPullsForm.ids.length > 0" @close="deleteUssdPullsForm.reset()">
        <template #title>
            {{ t('message.ussd_pulls.delete', deleteUssdPullsForm.ids.length) }}
        </template>

        <template #content>
            {{ t('message.ussd_pulls.delete_confirmation', deleteUssdPullsForm.ids.length) }}
        </template>

        <template #footer>
            <SecondaryButton @click="deleteUssdPullsForm.reset()">
                {{ t('action.cancel') }}
            </SecondaryButton>

            <DangerButton
                :class="{ 'opacity-25': deleteUssdPullsForm.processing }"
                :disabled="deleteUssdPullsForm.processing"
                class="ml-3"
                @click="deleteUssdPulls"
            >
                {{ t('action.delete') }}
            </DangerButton>
        </template>
    </ConfirmationModal>

    <!-- Retry USSD Pulls Confirmation Modal -->
    <ConfirmationModal :show="retryUssdPullsForm.ids.length > 0" @close="retryUssdPullsForm.reset()">
        <template #title>
            {{ t('message.ussd_pulls.retry', retryUssdPullsForm.ids.length) }}
        </template>

        <template #content>
            {{ t('message.ussd_pulls.retry_confirmation', retryUssdPullsForm.ids.length) }}
        </template>

        <template #footer>
            <SecondaryButton @click="retryUssdPullsForm.reset()">
                {{ t('action.cancel') }}
            </SecondaryButton>

            <DangerButton
                :class="{ 'opacity-25': retryUssdPullsForm.processing }"
                :disabled="retryUssdPullsForm.processing"
                class="ml-3"
                @click="retryUssdPulls"
            >
                {{ t('action.retry') }}
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
