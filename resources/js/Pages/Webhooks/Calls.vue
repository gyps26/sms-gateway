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
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import List from '@/Pages/Webhooks/List.vue';
import { EyeIcon, PaperAirplaneIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    webhookCalls: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const webhookCallBeingShown = ref(null);
const webhookCallBeingRetried = ref(null);

const queryParams = reactive({
    'status': props.params.status,
    'event': props.params.event,
});

const payload = computed(() => webhookCallBeingShown.value?.payload);
const response = computed(() => webhookCallBeingShown.value?.response);

const retryWebhookCallForm = useForm({});

const resendWebhookCall = () => {
    retryWebhookCallForm.post(route('webhook-calls.retry', webhookCallBeingRetried.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => webhookCallBeingRetried.value = null,
    });
};

const statusOptions = useEnums().webhookCallStatus;
const events = useEnums().webhookEvent;

const columns = [
    { name: t('field.url'), field: 'url', sortable: false },
    { name: t('field.event'), field: 'event' },
    { name: t('field.status'), field: 'status', render: (status) => statusOptions[status] },
    { name: t('field.status_code'), field: 'status_code', render: (data, row) => data ?? 'Not Available' },
    { name: t('field.attempts'), field: 'attempts' },
    {
        name: t('field.last_retry_at'),
        field: 'last_retry_at',
        render: (lastRetryAt) => lastRetryAt ? (new Date(lastRetryAt)).toLocaleString() : 'Never',
    },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => (new Date(createdAt)).toLocaleString() },
];

const actions = [
    {
        name: t('action.show'),
        icon: EyeIcon,
        callback: (webhookCall) => webhookCallBeingShown.value = webhookCall,
        screenReader: (webhookCall) => t('message.webhook_calls.action.show', { id: webhookCall.id }),
    },
    {
        name: t('action.retry'),
        icon: PaperAirplaneIcon,
        visible: (webhookCall) => webhookCall.status === 'Permanently Failed',
        callback: (webhookCall) => webhookCallBeingRetried.value = webhookCall,
        screenReader: (webhookCall) => t('message.webhook_calls.action.retry', { id: webhookCall.id }),
    },
];

watch(queryParams, () => useQueryFilter(queryParams, {}).refresh(['webhookCalls']));
</script>

<template>
    <List>
        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <InputLabel :value="t('field.status')" for="status" />
                        <ComboboxInput id="status" v-model="queryParams.status"
                                       :hideSearchBox="true"
                                       :options="statusOptions"
                                       class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.event')" for="event" />
                        <ComboboxInput id="event" v-model="queryParams.event"
                                       :hideSearchBox="true"
                                       :options="events"
                                       class="mt-1 block w-full" />
                    </div>
                </div>
            </div>
        </div>

        <DataTable :actions="actions" :collection="webhookCalls" :columns="columns" :only="['webhookCalls']"
                   :params="params" />

        <!-- View Webhook Call Modal -->
        <DialogModal :show="webhookCallBeingShown !== null" @close="webhookCallBeingShown = null">
            <template #title>
                {{ t('message.webhook_calls.show') }}
            </template>

            <template #content>
                <div class="space-y-4">
                    <div>
                        <InputLabel :value="t('field.payload')" for="payload" />
                        <TextAreaInput id="payload" v-model="payload" aria-readonly="true" autofocus
                                       class="mt-1 block w-full" readonly />
                    </div>
                    <div>
                        <InputLabel :value="t('field.response')" for="response" />
                        <TextAreaInput id="response" v-model="response" aria-readonly="true" class="mt-1 block w-full"
                                       readonly />
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="webhookCallBeingShown = null">
                    {{ t('action.close') }}
                </SecondaryButton>
            </template>
        </DialogModal>

        <!-- Retry Webhook Call Confirmation Modal -->
        <ConfirmationModal :show="webhookCallBeingRetried != null" @close="webhookCallBeingRetried = null">
            <template #title>
                {{ t('message.webhook_calls.retry') }}
            </template>

            <template #content>
                {{ t('message.webhook_calls.retry_confirmation', { id: webhookCallBeingRetried.id }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="webhookCallBeingRetried = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': retryWebhookCallForm.processing }"
                    :disabled="retryWebhookCallForm.processing"
                    class="ml-3"
                    @click="resendWebhookCall"
                >
                    {{ t('action.retry') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>
    </List>
</template>
