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
import InputLabel from '@/Components/InputLabel.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { TrashIcon } from '@heroicons/vue/20/solid';
import { PaperAirplaneIcon } from "@heroicons/vue/20/solid/index.js";
import { router, useForm } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';

const props = defineProps({
    calls: {
        type: Object,
        required: true,
    },
    sims: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const queryParams = reactive({
    type: props.params.type,
    sim: props.params.sim,
    answered: props.params.answered,
});

const deleteCallsForm = useForm({
    ids: [],
    all: false,
    search: null,
});

const typeOptions = useEnums().callType;

const columns = [
    { name: t('field.phone_number'), field: 'number' },
    {
        name: t('field.type'),
        field: 'type',
        render: (type) => typeOptions[type],
        sortable: false,
    },
    { name: t('field.duration'), field: 'duration', sortable: false },
    {
        name: t('entity.sim'),
        field: 'sim_id',
        render: (simId) => props.sims[simId]?.label ?? `SIM #${ simId }`,
        sortable: false
    },
    { name: t('field.started_at'), field: 'started_at', render: (startedAt) => new Date(startedAt).toLocaleString() },
];

const defaults = {
    sort_by: 'started_at',
};

const actions = [
    {
        name: t('action.send'),
        icon: PaperAirplaneIcon,
        callback: (call) => router.visit(route('messages.create', {
            recipients: 'mobile_numbers',
            mobile_numbers: [call.number],
            sims: [call.sim_id]
        })),
        screenReader: (call) => t('message.call_log.action.send', { number: call.number }),
    },
]

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(deleteCallsForm, params),
        },
    ],
];

const deleteCalls = () => {
    deleteCallsForm.post(route('calls.delete'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteCallsForm.reset(),
    });
};

watch(queryParams, () => useQueryFilter(queryParams, {}).refresh(['calls']));
</script>

<template>
    <ContentLayout :title="t('page.call_log')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.call_log') }}
                </h1>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <InputLabel :value="t('field.type')" for="type" />
                        <ComboboxInput id="type" v-model="queryParams.type"
                                       :hideSearchBox="true"
                                       :options="typeOptions"
                                       class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel :value="t('entity.sim')" for="sim" />
                        <ComboboxInput id="sim" v-model="queryParams.sim"
                                       :options="Object.values(props.sims)"
                                       class="mt-1 block w-full"
                                       text-attribute="label"
                                       value-attribute="id" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.answered')" for="answered" />
                        <ComboboxInput id="answered" v-model="queryParams.answered"
                                       :options="[{ label: t('action.yes'), value: 1 }, { label: t('action.no'), value: 0 }]"
                                       class="mt-1 block w-full" />
                    </div>
                </div>
            </div>
        </div>

        <DataTable :actions="actions" :bulk-actions="bulkActions" :collection="calls" :columns="columns"
                   :defaults="defaults"
                   :only="['calls']" :params="params" />

        <!-- Delete Calls Confirmation Modal -->
        <ConfirmationModal :show="deleteCallsForm.ids.length > 0" @close="deleteCallsForm.reset()">
            <template #title>
                {{ t('message.call_log.delete', deleteCallsForm.ids.length) }}
            </template>

            <template #content>
                {{ t('message.call_log.delete_confirmation', deleteCallsForm.ids.length) }}
            </template>

            <template #footer>
                <SecondaryButton @click="deleteCallsForm.reset()">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteCallsForm.processing }"
                    :disabled="deleteCallsForm.processing"
                    class="ml-3"
                    @click="deleteCalls"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
