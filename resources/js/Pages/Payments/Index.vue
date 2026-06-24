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
import DangerButton from "@/Components/DangerButton.vue";
import DataTable from '@/Components/DataTable.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { CheckIcon, DocumentTextIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { useForm, usePage } from '@inertiajs/vue3';
import { reactive, ref, watch } from 'vue';

const props = defineProps({
    payments: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    paymentMethods: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();
const page = usePage();

const paymentBeingApproved = ref(null);
const paymentBeingDeclined = ref(null);

const approvePaymentForm = useForm({});
const declinePaymentForm = useForm({});

const queryParams = reactive({
    method: props.params.method,
    user: props.params.user,
    subscription: props.params.subscription,
    status: props.params.status,
});

const statusOptions = useEnums().paymentStatus;

const columns = [
    { name: t('field.transaction_id'), field: 'transaction_id' },
    { name: t('field.method'), field: 'subscription.payment_method', sortable: false },
    {
        name: t('entity.user'),
        field: 'subscription.user_id',
        render: (userId) => props.users[userId].label,
        sortable: false,
        visible: page.props.auth.user.is_admin && ! props.params.subscription,
    },
    { name: t('field.amount'), field: 'amount' },
    { name: t('field.currency'), field: 'currency' },
    { name: t('field.status'), field: 'status', render: (status) => statusOptions[status] },
    { name: t('field.created_at'), field: 'created_at', render: (created_at) => new Date(created_at).toLocaleString() },
];

const actions = [
    {
        name: t('action.view_invoice'),
        icon: DocumentTextIcon,
        callback: (payment) => window.open(route('payments.invoice', payment), 'mozillaTab'),
        screenReader: (payment) => t('message.payments.action.view_invoice', { id: payment.id }),
    },
    {
        name: t('action.approve'),
        icon: CheckIcon,
        callback: (payment) => paymentBeingApproved.value = payment,
        screenReader: (payment) => t('message.payments.action.approve', { id: payment.transaction_id }),
        visible: (payment) => page.props.auth.user.is_admin && payment.status === 'Pending' && payment.subscription.payment_method === 'Bank Transfer',
    },
    {
        name: t('action.decline'),
        icon: XMarkIcon,
        callback: (payment) => paymentBeingDeclined.value = payment,
        screenReader: (payment) => t('message.payments.action.decline', { id: payment.transaction_id }),
        visible: (payment) => page.props.auth.user.is_admin && payment.status === 'Pending' && payment.subscription.payment_method === 'Bank Transfer',
    },
];

const approvePayment = () => {
    approvePaymentForm.post(route('payments.approve', { payment: paymentBeingApproved.value }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => paymentBeingApproved.value = null,
    });
};

const declinePayment = () => {
    declinePaymentForm.post(route('payments.decline', { payment: paymentBeingDeclined.value }), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => paymentBeingDeclined.value = null,
    });
};

watch(queryParams, () => useQueryFilter(queryParams).refresh(['payments']));
</script>

<template>
    <ContentLayout :title="t('page.payments')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.payments') }}
                </h1>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div v-if="page.props.auth.user.is_admin && ! props.params.subscription">
                        <InputLabel :value="t('entity.user')" for="user" />
                        <ComboboxInput id="user" v-model="queryParams.user"
                                       :options="Object.values(users)"
                                       class="mt-1 block w-full"
                                       value-attribute="id" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.status')" for="status" />
                        <ComboboxInput id="status" v-model="queryParams.status"
                                       :hideSearchBox="true"
                                       :options="statusOptions"
                                       class="mt-1 block w-full" />
                    </div>
                    <div>
                        <InputLabel :value="t('field.method')" for="method" />
                        <ComboboxInput id="method" v-model="queryParams.method"
                                       :hideSearchBox="true"
                                       :options="paymentMethods"
                                       class="mt-1 block w-full" />
                    </div>
                </div>
            </div>
        </div>

        <DataTable :actions="actions" :collection="payments" :columns="columns" :only="['payments']" :params="params" />

        <!-- Approve Payment Confirmation Modal -->
        <ConfirmationModal :show="paymentBeingApproved !== null" @close="paymentBeingApproved = null">
            <template #title>
                {{ t('message.payments.approve') }}
            </template>

            <template #content>
                {{ t('message.payments.approve_confirmation', { id: paymentBeingApproved?.transaction_id }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="paymentBeingApproved = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': approvePaymentForm.processing }"
                    :disabled="approvePaymentForm.processing"
                    class="ml-3"
                    @click="approvePayment"
                >
                    {{ t('action.approve') }}
                </PrimaryButton>
            </template>
        </ConfirmationModal>

        <!-- Decline Payment Confirmation Modal -->
        <ConfirmationModal :show="paymentBeingDeclined !== null" @close="paymentBeingDeclined = null">
            <template #title>
                {{ t('message.payments.decline') }}
            </template>

            <template #content>
                {{ t('message.payments.decline_confirmation', { id: paymentBeingDeclined?.transaction_id }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="paymentBeingDeclined = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': declinePaymentForm.processing }"
                    :disabled="declinePaymentForm.processing"
                    class="ml-3"
                    @click="declinePayment"
                >
                    {{ t('action.decline') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
