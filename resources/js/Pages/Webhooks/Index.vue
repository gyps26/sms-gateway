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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import List from '@/Pages/Webhooks/List.vue';
import { PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { cloneDeep } from 'lodash';
import { computed, ref } from 'vue';

const props = defineProps({
    webhooks: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingWebhook = ref(false);
const webhookBeingDeleted = ref(null);
const webhookBeingUpdated = ref(null);

const managingWebhook = computed({
    get() {
        return creatingWebhook.value || webhookBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                Object.assign(manageWebhookForm, cloneDeep(val));
                webhookBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingWebhook.value = val;
        } else {
            creatingWebhook.value = false;
            webhookBeingUpdated.value = null;
            manageWebhookForm.reset();
            manageWebhookForm.clearErrors();
        }
    },
});

const manageWebhookForm = useForm({
    url: null,
    secret: null,
    events: [],
});

const deleteWebhookForm = useForm({});

const columns = [
    { name: t('field.url'), field: 'url' },
    { name: t('field.events'), field: 'events', sortable: false },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => (new Date(createdAt)).toLocaleString() },
    { name: t('field.updated_at'), field: 'updated_at', render: (updatedAt) => (new Date(updatedAt)).toLocaleString() },
];

const events = useEnums().webhookEvent;

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (webhook) => managingWebhook.value = webhook,
        screenReader: (webhook) => t('message.webhooks.action.edit', { webhook: webhook.url }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (webhook) => webhookBeingDeleted.value = webhook,
        screenReader: (webhook) => t('message.webhooks.action.delete', { webhook: webhook.url }),
    },
];

const manageWebhook = () => {
    if (webhookBeingUpdated.value) {
        manageWebhookForm.put(route('webhooks.update', webhookBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingWebhook.value = false,
        });
    } else {
        manageWebhookForm.post(route('webhooks.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingWebhook.value = false,
        });
    }
};

const deleteWebhook = () => {
    deleteWebhookForm.delete(route('webhooks.destroy', webhookBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => webhookBeingDeleted.value = null,
    });
};
</script>

<template>
    <List>
        <template #actions>
            <PrimaryButton type="button" @click="managingWebhook = true">
                <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                {{ t('action.add') }}
            </PrimaryButton>
        </template>

        <DataTable :actions="actions" :collection="webhooks" :columns="columns" :params="params" />

        <!-- Manage Webhook Modal -->
        <DialogModal :show="managingWebhook" @close="managingWebhook = false">
            <template #title>
                {{ webhookBeingUpdated ? t('message.webhooks.edit') : t('message.webhooks.add') }}
            </template>

            <template #content>
                <form id="manage-webhook" @submit.prevent="manageWebhook">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.url')" for="url" required />
                            <TextInput id="url" v-model="manageWebhookForm.url" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageWebhookForm.errors.url" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.secret')" for="secret" />
                            <TextInput id="secret" v-model="manageWebhookForm.secret" class="mt-1 block w-full"
                                       type="password" />
                            <InputError :message="manageWebhookForm.errors.secret" class="mt-2" />
                        </div>
                        <div>
                            <fieldset class="p-4 pb-6 border border-solid border-gray-300 rounded-md">
                                <legend>{{ t('field.events') }} <span class="text-red-600">*</span></legend>
                                <div class="space-y-4">
                                    <div v-for="event in events" :key="event" class="flex items-center">
                                        <Checkbox :id="event" v-model:checked="manageWebhookForm.events"
                                                  :value="event" />
                                        <InputLabel :for="event" :value="event" class="ml-3" />
                                    </div>
                                </div>
                            </fieldset>
                            <InputError :message="manageWebhookForm.errors.events" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingWebhook = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageWebhookForm.processing }"
                    :disabled="manageWebhookForm.processing"
                    class="ml-3"
                    form="manage-webhook"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Webhook Confirmation Modal -->
        <ConfirmationModal :show="webhookBeingDeleted != null" @close="webhookBeingDeleted = null">
            <template #title>
                {{ t('message.webhooks.delete') }}
            </template>

            <template #content>
                {{ t('message.webhooks.delete_confirmation', { webhook: webhookBeingDeleted?.url }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="webhookBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteWebhookForm.processing }"
                    :disabled="deleteWebhookForm.processing"
                    class="ml-3"
                    @click="deleteWebhook"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </List>
</template>
