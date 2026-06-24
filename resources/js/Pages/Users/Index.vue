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
import DialogModal from "@/Components/DialogModal.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from "@/Components/TextInput.vue";
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { ArrowRightStartOnRectangleIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    users: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingUser = ref(false);
const userBeingDeleted = ref(null);

const createUserForm = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
});

const deleteUserForm = useForm({});

const columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('field.name'), field: 'name' },
    { name: t('field.email'), field: 'email' },
    { name: t('field.calls'), field: 'calls' },
    { name: t('field.messages'), field: 'messages' },
    { name: t('field.ussd_pulls'), field: 'ussd_pulls' },
    { name: t('field.devices'), field: 'devices' },
    { name: t('field.sending_servers'), field: 'sending_servers' },
    { name: t('field.email_verified_at'), field: 'email_verified_at', render: (value) => value ?? t('message.never') },
    { name: t('field.created_at'), field: 'created_at' },
];

const actions = [
    {
        name: t('action.impersonate'),
        icon: ArrowRightStartOnRectangleIcon,
        callback: (user) => router.visit(route('impersonate', user.id)),
        visible: (user) => user.email_verified_at !== null,
        screenReader: (user) => t('message.users.action.impersonate', { user: user.email }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (user) => userBeingDeleted.value = user,
        screenReader: (user) => t('message.users.action.delete', { user: user.email }),
    },
];

const deleteUser = () => {
    deleteUserForm.delete(route('users.destroy', userBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => userBeingDeleted.value = null,
    });
};

const createUser = () => {
    createUserForm.post(route('users.store'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => creatingUser.value = false,
    });
};
</script>

<template>
    <ContentLayout :title="t('page.users')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.users') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingUser = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.create') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="users" :columns="columns" :only="['users']" :params="params" />

        <!-- Add User Modal -->
        <DialogModal :show="creatingUser" @close="creatingUser = false">
            <template #title>
                {{ t('message.users.create') }}
            </template>

            <template #content>
                <form id="create-user" @submit.prevent="createUser">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" />
                            <TextInput id="name" v-model="createUserForm.name" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="createUserForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.email')" for="email" required />
                            <TextInput id="email" v-model="createUserForm.email" class="mt-1 block w-full"
                                       required type="email" />
                            <InputError :message="createUserForm.errors.email" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.password')" for="password" required />
                            <TextInput id="password" v-model="createUserForm.password" class="mt-1 block w-full"
                                       required type="password" />
                            <InputError :message="createUserForm.errors.password" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.password_confirmation')" for="password_confirmation" required />
                            <TextInput id="password_confirmation" v-model="createUserForm.password_confirmation"
                                       class="mt-1 block w-full" required type="password" />
                            <InputError :message="createUserForm.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="creatingUser = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': createUserForm.processing }"
                    :disabled="createUserForm.processing"
                    class="ml-3"
                    form="create-user"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete User Confirmation Modal -->
        <ConfirmationModal :show="userBeingDeleted != null" @close="userBeingDeleted = null">
            <template #title>
                {{ t('message.users.action.delete') }}
            </template>

            <template #content>
                {{ t('message.users.delete_confirmation', { user: userBeingDeleted?.email }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="userBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteUserForm.processing }"
                    :disabled="deleteUserForm.processing"
                    class="ml-3"
                    @click="deleteUser"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
