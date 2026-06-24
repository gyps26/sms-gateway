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
import Checkbox from '@/Components/Checkbox.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from "@/Composables/useEnums.js";
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import UpdateQuotaDialog from '@/Pages/Quota/UpdateDialog.vue';
import { PencilSquareIcon, PlusIcon, ScaleIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { cloneDeep, forEach } from 'lodash';
import { computed, ref } from 'vue';

const props = defineProps({
    sendingServers: {
        type: Object,
        required: true,
    },
    drivers: {
        type: Object,
        required: true,
    },
    timezones: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const sendingServerBeingDeleted = ref(null);
const sendingServerBeingUpdated = ref(null);
const creatingSendingServer = ref(false);
const quotaBeingUpdated = ref(null);

const managingSendingServer = computed({
    get() {
        return creatingSendingServer.value || sendingServerBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                Object.assign(manageSendingServerForm, cloneDeep(val));
                sendingServerBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingSendingServer.value = val;
        } else {
            creatingSendingServer.value = false;
            sendingServerBeingUpdated.value = null;
            manageSendingServerForm.reset();
            manageSendingServerForm.clearErrors();
        }
    },
});

const deleteSendingServerForm = useForm({});

const manageSendingServerForm = useForm({
    'name': null,
    'supported_types': [],
    'driver': null,
    'config': {},
    'enabled': true,
});

const frequencyUnits = useEnums().frequencyUnit;

const columns = [
    { name: t('field.name'), field: 'name' },
    { name: t('field.driver'), field: 'driver' },
    { name: t('field.supported_types'), field: 'supported_types', sortable: false },
    { name: t('field.enabled'), field: 'enabled' },
    {
        name: t('entity.quota'),
        field: 'quota',
        render: (quota) => {
            return quota.enabled
                ? quota.frequency === 1
                    ? `${ quota.value }/${ frequencyUnits[quota.frequency_unit] }`
                    : `${ quota.value }/${ quota.frequency } ${ frequencyUnits[quota.frequency_unit] }`
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
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => (new Date(createdAt)).toLocaleString() },
    { name: t('field.updated_at'), field: 'updated_at', render: (updatedAt) => (new Date(updatedAt)).toLocaleString() },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (sendingServer) => managingSendingServer.value = sendingServer,
        screenReader: (sendingServer) => t('message.sending_servers.action.edit', { sendingServer: sendingServer.name }),
    },
    {
        name: t('message.quota.edit'),
        icon: ScaleIcon,
        callback: (sendingServer) => quotaBeingUpdated.value = sendingServer.quota,
        screenReader: (sendingServer) => t('message.sending_servers.action.edit_quota', { sendingServer: sendingServer.name }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (sendingServer) => sendingServerBeingDeleted.value = sendingServer,
        screenReader: (sendingServer) => t('message.sending_servers.action.delete', { sendingServer: sendingServer.name }),
    },
];

const deleteSendingServer = () => {
    deleteSendingServerForm.delete(route('sending-servers.destroy', sendingServerBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => sendingServerBeingDeleted.value = null,
    });
};

const manageSendingServer = () => {
    if (sendingServerBeingUpdated.value) {
        manageSendingServerForm.put(route('sending-servers.update', sendingServerBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingSendingServer.value = false,
        });
    } else {
        manageSendingServerForm.post(route('sending-servers.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingSendingServer.value = false,
        });
    }
};

const setConfig = (driver) => {
    if (driver) {
        forEach(props.drivers[driver]['fields'], (field, key) => {
            if (field.default) {
                manageSendingServerForm.config[key] = cloneDeep(field.default);
                return;
            }

            switch (field.type) {
                case 'boolean':
                    manageSendingServerForm.config[key] = false;
                    break;
                case 'number':
                    manageSendingServerForm.config[key] = 0;
                    break;
                case 'dictionary':
                    manageSendingServerForm.config[key] = [];
                    break;
                default:
                    manageSendingServerForm.config[key] = null;
                    break;
            }
        });
    }
};
</script>

<template>
    <ContentLayout :title="t('page.sending_servers')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.sending_servers') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="managingSendingServer = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="sendingServers" :columns="columns" :only="['sendingServers']"
                   :params="params" />

        <!-- Delete Sending Server Confirmation Modal -->
        <ConfirmationModal :show="sendingServerBeingDeleted != null" @close="sendingServerBeingDeleted = null">
            <template #title>
                {{ t('message.sending_servers.delete') }}
            </template>

            <template #content>
                {{
                    t('message.sending_servers.delete_confirmation', { sendingServer: sendingServerBeingDeleted.name })
                }}
            </template>

            <template #footer>
                <SecondaryButton @click="sendingServerBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteSendingServerForm.processing }"
                    :disabled="deleteSendingServerForm.processing"
                    class="ml-3"
                    @click="deleteSendingServer"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>

        <!-- Manage Sending Server Modal -->
        <DialogModal :show="managingSendingServer" @close="managingSendingServer = false">
            <template #title>
                {{ sendingServerBeingUpdated ? t('message.sending_servers.edit') : t('message.sending_servers.add') }}
            </template>

            <template #content>
                <form id="manage-sending-server" @submit.prevent="manageSendingServer">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" required />
                            <TextInput id="name" v-model="manageSendingServerForm.name" autofocus
                                       class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageSendingServerForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.driver')" for="driver" required />
                            <ComboboxInput id="driver" v-model="manageSendingServerForm.driver"
                                           :options="Object.values(drivers)"
                                           class="mt-1 block w-full" text-attribute="name" value-attribute="driver"
                                           @change="setConfig" />
                            <InputError :message="manageSendingServerForm.errors.driver" class="mt-2" />
                        </div>
                        <div v-if="manageSendingServerForm.driver">
                            <fieldset class="p-4 pb-6 border border-solid border-gray-300 rounded-md">
                                <legend>{{ t('field.supported_types') }} <span class="text-red-600">*</span></legend>
                                <div class="space-y-4">
                                    <div
                                        v-for="supportedType in drivers[manageSendingServerForm.driver]['supported_types']"
                                        :key="supportedType" class="flex items-center">
                                        <Checkbox :id="supportedType.toLowerCase()"
                                                  v-model:checked="manageSendingServerForm.supported_types"
                                                  :value="supportedType" />
                                        <InputLabel :for="supportedType.toLowerCase()" :value="supportedType"
                                                    class="ml-3" />
                                    </div>
                                </div>
                            </fieldset>
                            <InputError :message="manageSendingServerForm.errors.supported_types" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput v-model="manageSendingServerForm.enabled" :label="t('field.enabled')" />
                        </div>
                        <template v-if="manageSendingServerForm.driver">
                            <div class="relative">
                                <div aria-hidden="true" class="absolute inset-0 flex items-center">
                                    <div class="w-full border-t border-gray-300" />
                                </div>
                                <div class="relative flex justify-center">
                                <span class="bg-white px-3 text-base font-semibold leading-6 text-gray-900">
                                    <label class="flex items-center">
                                        <span class="text-sm">{{ t('field.configurations') }}</span>
                                    </label>
                                </span>
                                </div>
                            </div>
                            <template v-for="(setting, field) in drivers[manageSendingServerForm.driver]['fields']"
                                      :key="field">
                                <div v-if="setting.type === 'list'">
                                    <InputLabel :for="field" :value="setting.label" :required="setting.required" />
                                    <ComboboxInput :id="field" v-model="manageSendingServerForm.config[field]"
                                                   :options="setting.options" :clearable="! setting.required" class="mt-1 block w-full" />
                                    <InputError :message="manageSendingServerForm.errors[`config.${field}`]"
                                                class="mt-2" />
                                </div>
                                <div v-else-if="setting.type === 'text' || setting.type === 'number'">
                                    <InputLabel :for="field" :value="setting.label" :required="setting.required" />
                                    <TextInput :id="field" v-model="manageSendingServerForm.config[field]"
                                               :type="setting.type"
                                               class="mt-1 block w-full"
                                               :required="setting.required" />
                                    <InputError :message="manageSendingServerForm.errors[`config.${field}`]"
                                                class="mt-2" />
                                </div>
                                <div v-else-if="setting.type === 'boolean'">
                                    <SwitchInput :id="field" v-model="manageSendingServerForm.config[field]"
                                                 :label="setting.label" />
                                </div>
                                <template v-else>
                                    <fieldset class="p-4 pb-6 border border-solid border-gray-300 rounded-md">
                                        <legend>{{ setting.label }}</legend>
                                        <div class="space-y-4">
                                            <div v-if="Array.isArray(manageSendingServerForm.config[field])">
                                                <PrimaryButton type="button"
                                                               @click="manageSendingServerForm.config[field].push({ key: '', value: '' })">
                                                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                                    {{ t('action.add') }}
                                                    <span class="sr-only">{{ setting.label }}</span>
                                                </PrimaryButton>
                                            </div>
                                            <div v-for="(kvp, index) in manageSendingServerForm.config[field]"
                                                 class="grid grid-cols-1 gap-3 md:grid-cols-[repeat(3,_1fr)] md:place-items-start">
                                                <div>
                                                    <TextInput v-model="kvp.key" :title="`KVP #${index + 1} Key`"
                                                               required type="text" class="w-full" />
                                                    <InputError :message="manageSendingServerForm.errors[`config.${field}.${index}.key`]"
                                                                class="mt-2" />
                                                </div>
                                                <div>
                                                    <TextInput v-model="kvp.value" :title="`KVP #${index + 1} Value`"
                                                               required type="text" class="w-full" />
                                                    <InputError :message="manageSendingServerForm.errors[`config.${field}.${index}.value`]"
                                                                class="mt-2" />
                                                </div>
                                                <div v-if="! setting.required || manageSendingServerForm.config[field].length > 1" class="pt-1">
                                                    <DangerButton type="button"
                                                                  @click="manageSendingServerForm.config[field].splice(index, 1)">
                                                        <TrashIcon aria-hidden="true" class="h-4 w-4"></TrashIcon>
                                                        <span class="sr-only">
                                                            {{ t('action.delete') }}, {{ setting.label }} #{{ index + 1 }}
                                                        </span>
                                                    </DangerButton>
                                                </div>
                                                <hr v-if="manageSendingServerForm.config[field].length !== index + 1"
                                                    class="md:hidden">
                                            </div>
                                        </div>
                                    </fieldset>
                                </template>
                            </template>
                        </template>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingSendingServer = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageSendingServerForm.processing }"
                    :disabled="manageSendingServerForm.processing"
                    class="ml-3"
                    form="manage-sending-server"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit Quota Modal -->
        <UpdateQuotaDialog v-model="quotaBeingUpdated" :timezones="timezones" />
    </ContentLayout>
</template>

