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
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import UpdateQuotaDialog from '@/Pages/Quota/UpdateDialog.vue';
import { PencilSquareIcon, ScaleIcon } from '@heroicons/vue/20/solid';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sims: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
    timezones: {
        type: Array,
        required: true,
    },
});

const { t } = useI18n();

const simBeingUpdated = ref(null);
const quotaBeingUpdated = ref(null);

const updateSimForm = useForm({
    label: null,
});

const updateSim = () => {
    updateSimForm.put(route('sims.update', simBeingUpdated.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => simBeingUpdated.value = null,
    });
};

const frequencyUnits = useEnums().frequencyUnit;

const columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('field.label'), field: 'label', sortable: false },
    { name: t('field.name'), field: 'name' },
    { name: t('field.mobile_number'), field: 'number' },
    { name: t('field.country'), field: 'country' },
    { name: t('field.carrier'), field: 'carrier' },
    { name: t('field.signal_strength'), field: 'signal_strength', render: (signalStrength) => useEnums().signalStrength[signalStrength] },
    { name: t('field.slot'), field: 'slot' },
    { name: t('entity.device'), field: 'device.label', sortable: false },
    {
        name: t('entity.quota'),
        field: 'quota',
        render: (quota) => {
            return quota.enabled
                ? quota.frequency === 1 ? `${ quota.value }/${ frequencyUnits[quota.frequency_unit] }` : `${ quota.value }/${ quota.frequency } ${ frequencyUnits[quota.frequency_unit] }`
                : '∞';
        },
        sortable: false,
    },
    {
        name: t('field.available'),
        field: 'quota',
        render: (quota) => quota.enabled ? quota.available : '∞',
        sortable: false,
    },
    {
        name: t('field.reset_at'),
        field: 'quota',
        render: (quota) => quota.enabled ? (new Date(quota.reset_at)).toLocaleString() : 'Never',
        sortable: false,
    },
    { name: t('field.active'), field: 'active' },
    { name: t('field.updated_at'), field: 'updated_at' },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (sim) => {
            updateSimForm.label = sim.label;
            simBeingUpdated.value = sim.id;
        },
        visible: (sim) => sim.device.owner_id === usePage().props.auth.user.id,
        screenReader: (sim) => t('message.sims.action.edit', { sim: sim.label }),
    },
    {
        name: t('message.quota.edit'),
        icon: ScaleIcon,
        callback: (sim) => quotaBeingUpdated.value = sim.quota,
        visible: (sim) => sim.device.owner_id === usePage().props.auth.user.id,
        screenReader: (sim) => t('message.sims.action.edit_quota', { sim: sim.label }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.sims')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.sims') }}
                </h1>
            </div>
        </template>

        <DataTable :actions="actions" :collection="sims" :columns="columns" :only="['sims']" :params="params" />

        <!-- Edit Sim Modal -->
        <DialogModal :show="simBeingUpdated != null" @close="simBeingUpdated = null">
            <template #title>
                {{ t('message.sims.edit') }}
            </template>

            <template #content>
                <form id="update-sim" @submit.prevent="updateSim">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.label')" for="label" />
                            <TextInput id="label"
                                       v-model="updateSimForm.label" autofocus
                                       class="mt-1 block w-full" />
                            <InputError :message="updateSimForm.errors.label" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="simBeingUpdated = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updateSimForm.processing }"
                    :disabled="updateSimForm.processing"
                    class="ml-3"
                    form="update-sim"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit Quota Modal -->
        <UpdateQuotaDialog v-model="quotaBeingUpdated" :timezones="timezones" />
    </ContentLayout>
</template>
