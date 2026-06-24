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
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    blacklist: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingEntries = ref(false);

const createEntriesForm = useForm({
    mobile_numbers: null,
});

const deleteEntriesForm = useForm({
    ids: [],
    all: false,
    search: null,
});

const createEntries = () => {
    createEntriesForm.transform((data) => ({
        mobile_numbers: data.mobile_numbers?.split(/\r?\n/).filter(n => n.trim() !== ''),
    })).post(route('blacklist.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            creatingEntries.value = false;
            createEntriesForm.reset();
        },
    });
};

const deleteEntries = () => {
    deleteEntriesForm.post(route('blacklist.delete'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteEntriesForm.reset(),
    });
};

const columns = [
    { name: t('field.mobile_numbers'), field: 'mobile_number' },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => new Date(createdAt).toLocaleString() },
];

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(deleteEntriesForm, params),
        },
    ],
];
</script>

<template>
    <ContentLayout :title="t('page.blacklist')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.blacklist') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingEntries = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :bulk-actions="bulkActions" :collection="blacklist" :columns="columns" :params="params" />

        <!-- Add Mobile Numbers to Blacklist Modal -->
        <DialogModal :show="creatingEntries" @close="creatingEntries = false">
            <template #title>
                {{ t('message.blacklist.add') }}
            </template>

            <template #content>
                <form id="create-entries" @submit.prevent="createEntries">
                    <InputLabel :value="t('field.mobile_numbers')" for="mobile-numbers" required />
                    <TextAreaInput id="mobile-numbers" v-model="createEntriesForm.mobile_numbers"
                                   autofocus
                                   class="mt-1 block w-full"
                                   required />
                    <InputError :message="createEntriesForm.errors.mobile_numbers" class="mt-2" />
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="creatingEntries = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': createEntriesForm.processing }"
                    :disabled="createEntriesForm.processing"
                    class="ml-3"
                    form="create-entries"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Mobile Numbers from Blacklist Confirmation Modal -->
        <ConfirmationModal :show="deleteEntriesForm.ids.length > 0" @close="deleteEntriesForm.reset()">
            <template #title>
                {{ t('message.blacklist.delete', deleteEntriesForm.ids.length) }}
            </template>

            <template #content>
                {{ t('message.blacklist.delete_confirmation', deleteEntriesForm.ids.length) }}
            </template>

            <template #footer>
                <SecondaryButton @click="deleteEntriesForm.reset()">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteEntriesForm.processing }"
                    :disabled="deleteEntriesForm.processing"
                    class="ml-3"
                    @click="deleteEntries"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
