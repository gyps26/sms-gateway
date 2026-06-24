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
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { CogIcon, EyeIcon, PaperAirplaneIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    lists: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const contactListBeingDeleted = ref(null);
const creatingContactList = ref(false);

const deleteContactListForm = useForm({});
const createContactListForm = useForm({
    name: null,
});

const columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('field.name'), field: 'name' },
];

const actions = [
    {
        name: t('action.show'),
        icon: EyeIcon,
        callback: (contactList) => router.visit(route('contact-lists.contacts.index', contactList)),
        screenReader: (contactList) => t('message.contact_lists.action.show', { contactList: contactList.name }),
    },
    {
        name: t('page.fields'),
        icon: CogIcon,
        callback: (contactList) => router.visit(route('contact-lists.edit', contactList)),
        screenReader: (contactList) => t('message.contact_lists.action.manage_fields', { contactList: contactList.name }),
    },
    {
        name: t('action.send'),
        icon: PaperAirplaneIcon,
        callback: (contactList) => router.visit(route('messages.create', { recipients: 'contact_lists', contact_lists: [contactList.id] })),
        screenReader: (contactList) => t('message.contact_lists.action.send', { contactList: contactList.name }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (contactList) => contactListBeingDeleted.value = contactList,
        screenReader: (contactList) => t('message.contact_lists.action.delete', { contactList: contactList.name }),
    },
];

const createContactList = () => {
    createContactListForm.post(route('contact-lists.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            creatingContactList.value = false;
            createContactListForm.reset();
        },
    });
};

const deleteContactList = () => {
    deleteContactListForm.delete(route('contact-lists.destroy', contactListBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => contactListBeingDeleted.value = null,
    });
};
</script>

<template>
    <ContentLayout :title="t('page.contact_lists')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.contact_lists') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingContactList = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="lists" :columns="columns" :params="params" />

        <!-- Add Contact List Modal -->
        <DialogModal :show="creatingContactList" @close="creatingContactList = false">
            <template #title>
                {{ t('message.contact_lists.add') }}
            </template>

            <template #content>
                <form id="create-contact-list" @submit.prevent="createContactList">
                    <InputLabel :value="t('field.name')" for="name" required />
                    <TextInput id="name" v-model="createContactListForm.name" autofocus class="mt-1 block w-full"
                               required type="text" />
                    <InputError :message="createContactListForm.errors.name" class="mt-2" />
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="creatingContactList = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': createContactListForm.processing }"
                    :disabled="createContactListForm.processing"
                    class="ml-3"
                    form="create-contact-list"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Contact List Confirmation Modal -->
        <ConfirmationModal :show="contactListBeingDeleted != null" @close="contactListBeingDeleted = null">
            <template #title>
                {{ t('message.contact_lists.delete') }}
            </template>

            <template #content>
                {{ t('message.contact_lists.delete_confirmation', { contactList: contactListBeingDeleted.name }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="contactListBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteContactListForm.processing }"
                    :disabled="deleteContactListForm.processing"
                    class="ml-3"
                    @click="deleteContactList"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
