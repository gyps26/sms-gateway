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
import LinkButton from '@/Components/LinkButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    ArrowDownTrayIcon,
    BookOpenIcon,
    DocumentTextIcon,
    PencilSquareIcon,
    PlusIcon,
    ShareIcon,
    TrashIcon
} from '@heroicons/vue/20/solid';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import TextAreaInput from "@/Components/TextAreaInput.vue";

const props = defineProps({
    devices: {
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

const addingDevice = ref(false);
const deviceBeingUpdated = ref(null);
const deviceBeingShared = ref(null);
const deviceBeingDeleted = ref(null);
const showingLogs = ref(null);

const updateDeviceForm = useForm({
    name: null,
});

const deleteDeviceForm = useForm({});

const shareForm = useForm({
    users: [],
});

const updateDevice = () => {
    updateDeviceForm.put(route('devices.update', deviceBeingUpdated.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deviceBeingUpdated.value = null,
    });
};

const deleteDevice = () => {
    deleteDeviceForm.delete(route('devices.destroy', deviceBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deviceBeingDeleted.value = null,
    });
};

const shareDevice = () => {
    shareForm.post(route('devices.share', deviceBeingShared.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deviceBeingShared.value = null,
    });
};

const columns = [
    { name: t('field.id'), field: 'id' },
    { name: t('field.name'), field: 'name' },
    { name: t('field.model'), field: 'model' },
    { name: t('field.enabled'), field: 'enabled' },
    { name: t('field.android_version'), field: 'android_version', sortable: false },
    { name: t('field.app_version'), field: 'app_version', sortable: false },
    { name: t('field.battery'), field: 'battery' },
    { name: t('field.charging'), field: 'is_charging' },
    { name: t('field.updated_at'), field: 'updated_at' },
];

if (usePage().props.auth.user.is_admin) {
    columns.push({ name: t('field.shared_with'), field: 'users', render: (users) => users.length, sortable: false });
}

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (device) => {
            updateDeviceForm.name = device.name;
            deviceBeingUpdated.value = device;
        },
        visible: (device) => device.owner_id === usePage().props.auth.user.id,
        screenReader: (device) => t('message.devices.action.edit', { device: device.name }),
    },
    {
        name: t('action.share'),
        icon: ShareIcon,
        callback: (device) => {
            shareForm.users = device.users;
            deviceBeingShared.value = device;
        },
        visible: () => usePage().props.auth.user.is_admin,
        screenReader: (device) => t('message.devices.action.share', { device: device.name }),
    },
    {
        name: t('action.show_logs'),
        icon: DocumentTextIcon,
        callback: (device) => showingLogs.value = device,
        visible: (device) => device.owner_id === usePage().props.auth.user.id,
        screenReader: (device) => t('message.devices.action.show_logs', { device: device.name }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (device) => deviceBeingDeleted.value = device,
        visible: (device) => device.owner_id === usePage().props.auth.user.id,
        screenReader: (device) => t('message.devices.action.delete', { device: device.name }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.devices')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.devices') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="addingDevice = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="devices" :columns="columns" :only="['devices']" :params="params" />

        <!-- Edit Device Modal -->
        <DialogModal :show="deviceBeingUpdated !== null" @close="deviceBeingUpdated = null">
            <template #title>
                {{ t('message.devices.edit') }}
            </template>

            <template #content>
                <form id="update-device" @submit.prevent="updateDevice">
                    <InputLabel :value="t('field.name')" for="name" />
                    <TextInput id="name" v-model="updateDeviceForm.name" autofocus class="mt-1 block w-full"
                               type="text" />
                    <InputError :message="updateDeviceForm.errors.name" class="mt-2" />
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="deviceBeingUpdated = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updateDeviceForm.processing }"
                    :disabled="updateDeviceForm.processing"
                    class="ml-3"
                    form="update-device"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Add Device Modal -->
        <DialogModal :show="addingDevice" @close="addingDevice = false">
            <template #title>
                {{ t('message.devices.add') }}
            </template>

            <template #content>
                <div class="text-sm">
                    <p class="mb-4">{{ t('message.devices.instructions.title') }}</p>
                    <ol class="list-decimal list-inside space-y-4">
                        <li>
                            <span>{{ t('message.devices.instructions.step1') }}</span>
                            <div class="flex justify-center my-4">
                                <LinkButton :href="$page.props.app.apk" download>
                                    <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                    {{ t('action.download') }}
                                </LinkButton>
                            </div>
                        </li>
                        <li>{{ t('message.devices.instructions.step2') }}</li>
                        <li>
                            <span>{{ t('message.devices.instructions.step3') }}</span>
                            <div class="flex justify-center my-4">
                                <LinkButton href="/docs/faqs.html#i-am-getting-app-blocked-to-protect-your-device-while-installing-the-app-why-is-that-and-how-to-solve-it" target="_blank">
                                    <BookOpenIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                    {{ t('action.unblock') }}
                                </LinkButton>
                            </div>
                        </li>
                        <li>
                            <span>{{ t('message.devices.instructions.step4') }}</span>
                            <span class="flex justify-center">
                                <img :alt="t('message.devices.qr_code')"
                                     :src="route('qr-code', { timestamp: Date.now() })" class="max-w-xs">
                            </span>
                        </li>
                        <li>{{ t('message.devices.instructions.step5') }}</li>
                    </ol>
                </div>
            </template>

            <template #footer>
                <PrimaryButton @click="addingDevice = false">
                    {{ t('action.close') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Devices Confirmation Modal -->
        <ConfirmationModal :show="deviceBeingDeleted != null" @close="deviceBeingDeleted = null">
            <template #title>
                {{ t('message.devices.delete') }}
            </template>

            <template #content>
                {{ t('message.devices.delete_confirmation', { device: deviceBeingDeleted.id }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="deviceBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteDeviceForm.processing }"
                    :disabled="deleteDeviceForm.processing"
                    class="ml-3"
                    @click="deleteDevice"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>

        <DialogModal :show="deviceBeingShared !== null" @close="deviceBeingShared = null">
            <template #title>
                {{ t('message.devices.share') }}
            </template>

            <template #content>
                <InputLabel :value="t('field.users')" for="users" />
                <ComboboxInput id="users" v-model="shareForm.users" :options="users"
                               class="mt-1 block w-full"
                               autofocus
                               multiple
                               value-attribute="id" />
                <InputError :message="shareForm.errors.users" class="mt-2" />
                <InputError
                    v-for="(pos, index) in shareForm.users.length"
                    :key="index" :message="shareForm.errors[`users.${index}`]"
                    class="mt-2" />
            </template>

            <template #footer>
                <SecondaryButton @click="deviceBeingShared = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': shareForm.processing }"
                    :disabled="shareForm.processing"
                    class="ml-3"
                    @click="shareDevice"
                >
                    {{ t('action.share') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <DialogModal :show="showingLogs !== null" @close="showingLogs = null">
            <template #title>
                {{ t('message.devices.logs') }}
            </template>

            <template #content>
                <TextAreaInput id="logs"
                               v-model="showingLogs.logs"
                               class="block w-full"
                               readonly />
            </template>

            <template #footer>
                <SecondaryButton @click="showingLogs = null">
                    {{ t('action.close') }}
                </SecondaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
