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
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { ShareIcon } from "@heroicons/vue/20/solid/index.js";
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sendingServers: {
        type: Array,
        required: true,
    },
    senderIds: {
        type: Object,
        required: true,
    },
    users: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const senderIdBeingDeleted = ref(null);
const senderIdBeingShared = ref(null);
const creatingSenderId = ref(false);

const deleteSenderIdForm = useForm({});

const createSenderIdForm = useForm({
    'sending_server': null,
    'value': null,
});

const shareForm = useForm({
    users: [],
});

const columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('entity.sender_id'), field: 'value' },
    {
        name: t('entity.sending_server'),
        field: 'sending_server.name',
        sortable: false,
    },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => (new Date(createdAt)).toLocaleString() },
];

if (usePage().props.auth.user.is_admin) {
    columns.push({ name: t('field.shared_with'), field: 'users', render: (users) => users.length, sortable: false });
}

const actions = [
    {
        name: t('action.share'),
        icon: ShareIcon,
        callback: (senderId) => {
            shareForm.users = senderId.users;
            senderIdBeingShared.value = senderId;
        },
        visible: () => usePage().props.auth.user.is_admin,
        screenReader: (senderId) => t('message.sender_ids.action.share', { senderId: senderId.value }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (senderId) => senderIdBeingDeleted.value = senderId,
        visible: (senderId) => senderId.sending_server.user_id === usePage().props.auth.user.id,
        screenReader: (senderId) => t('message.sender_ids.action.delete', { senderId: senderId.value }),
    },
];

const deleteSenderId = () => {
    deleteSenderIdForm.delete(route('sender-ids.destroy', senderIdBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => senderIdBeingDeleted.value = null,
    });
};

const createSenderId = () => {
    createSenderIdForm.post(route('sender-ids.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => creatingSenderId.value = false,
    });
};

const shareSenderId = () => {
    shareForm.post(route('sender-ids.share', senderIdBeingShared.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => senderIdBeingShared.value = null,
    });
};
</script>

<template>
    <ContentLayout :title="t('page.sender_ids')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.sender_ids') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingSenderId = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="senderIds" :columns="columns" :only="['senderIds']"
                   :params="params" />

        <!-- Delete Sender ID Confirmation Modal -->
        <ConfirmationModal :show="senderIdBeingDeleted != null" @close="senderIdBeingDeleted = null">
            <template #title>
                {{ t('message.sender_ids.delete') }}
            </template>

            <template #content>
                {{ t('message.sender_ids.delete_confirmation', { senderId: senderIdBeingDeleted.value }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="senderIdBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteSenderIdForm.processing }"
                    :disabled="deleteSenderIdForm.processing"
                    class="ml-3"
                    @click="deleteSenderId"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Create Sender ID Modal -->
        <DialogModal :show="creatingSenderId" @close="creatingSenderId = false">
            <template #title>
                {{ t('message.sender_ids.add') }}
            </template>

            <template #content>
                <form id="create-sender-id" @submit.prevent="createSenderId">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('entity.sending_server')" for="sending-server" required />
                            <ComboboxInput id="sending-server" v-model="createSenderIdForm.sending_server"
                                           :clearable="false"
                                           :options="Object.values(sendingServers)"
                                           autofocus
                                           class="mt-1 block w-full"
                                           text-attribute="name"
                                           value-attribute="id" />
                            <InputError :message="createSenderIdForm.errors.sending_server" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.value')" for="value" required />
                            <TextInput id="value" v-model="createSenderIdForm.value" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="createSenderIdForm.errors.value" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="creatingSenderId = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': createSenderIdForm.processing }"
                    :disabled="createSenderIdForm.processing"
                    class="ml-3"
                    form="create-sender-id"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Share Sender ID Modal -->
        <DialogModal :show="senderIdBeingShared !== null" @close="senderIdBeingShared = null">
            <template #title>
                {{ t('message.sender_ids.share') }}
            </template>

            <template #content>
                <InputLabel :value="t('field.users')" for="users" />
                <ComboboxInput id="users" v-model="shareForm.users" :options="users"
                               autofocus
                               class="mt-1 block w-full"
                               multiple
                               value-attribute="id" />
                <InputError :message="shareForm.errors.users" class="mt-2" />
                <InputError
                    v-for="(pos, index) in shareForm.users.length"
                    :key="index" :message="shareForm.errors[`users.${index}`]"
                    class="mt-2" />
            </template>

            <template #footer>
                <SecondaryButton @click="senderIdBeingShared = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': shareForm.processing }"
                    :disabled="shareForm.processing"
                    class="ml-3"
                    @click="shareSenderId"
                >
                    {{ t('action.share') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>

