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
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import ToggleableInput from "@/Components/ToggleableInput.vue";
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { BanknotesIcon, UserPlusIcon, XMarkIcon, PencilSquareIcon } from '@heroicons/vue/20/solid';
import { router, usePage, useForm } from '@inertiajs/vue3';
import { map } from "lodash";
import { reactive, watch, ref } from 'vue';

const props = defineProps({
    subscriptions: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    plans: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();
const page = usePage();

const queryParams = reactive({
    plan: props.params.plan,
    user: props.params.user,
    status: props.params.status,
});

const statusOptions = useEnums().subscriptionStatus;

const assigningSubscription = ref(false);
const subscriptionBeingCancelled = ref(null);
const subscriptionBeingUpdated = ref(null);

const cancelSubcriptionForm = useForm({
    immediate: false,
});

const assignSubscriptionForm = useForm({
    user: null,
    plan: null,
    cycles: null,
});

const updateSubscriptionForm = useForm({
    features: {
        credits: null,
    }
});

const assignSubscription = () => {
    assignSubscriptionForm.post(route('subscriptions.assign'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            assigningSubscription.value = false;
            assignSubscriptionForm.reset();
        },
    });
};

const cancelSubscription = (immediate) => {
    cancelSubcriptionForm.transform(() => ({ immediate: immediate}))
        .post(route('subscriptions.cancel', subscriptionBeingCancelled.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                subscriptionBeingCancelled.value = null;
                cancelSubcriptionForm.reset();
            }
        });
};

const updateSubscription = () => {
    updateSubscriptionForm.put(route('subscriptions.update', subscriptionBeingUpdated.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => subscriptionBeingUpdated.value = null,
    });
};

const columns = [
    { name: t('field.id'), field: 'subscription_id', sortable: false },
    { name: t('entity.plan'), field: 'plan_id', render: (planId) => props.plans[planId].label },
    { name: t('field.status'), field: 'status', render: (status) => statusOptions[status] },
    { name: t('field.quota'), field: 'features', render: (features) => map(features, (value, key) => `${t(`field.${ key }`)}: ${value === null ? '∞': value }`), sortable: false },
    { name: t('field.payment_method'), field: 'payment_method' },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => new Date(createdAt).toLocaleString() },
    {
        name: t('field.renewal_at'),
        field: 'renewal_at',
        render: (renewalAt) => renewalAt ? new Date(renewalAt).toLocaleString() : 'Never',
    },
    {
        name: t('field.ends_at'),
        field: 'ends_at',
        render: (endsAt) => endsAt ? new Date(endsAt).toLocaleString() : 'Never',
    },
];

if (page.props.auth.user.is_admin) {
    columns.splice(2, 0, { name: t('entity.user'), field: 'user_id', render: (userId) => props.users[userId].label });
}

const isImmediatelyCancelable = (subscription) => {
    return page.props.auth.user.is_admin && (subscription.ends_at === null || new Date(subscription.ends_at) > new Date());
};

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (subscription) => {
            updateSubscriptionForm.features.credits = subscription.features.credits;
            subscriptionBeingUpdated.value = subscription;
        },
        screenReader: (subscription) => t('message.subscriptions.action.edit', { id: subscription.id }),
        visible: () => page.props.auth.user.is_admin,
    },
    {
        name: t('page.payments'),
        icon: BanknotesIcon,
        callback: (subscription) => router.visit(route('payments.index', { subscription: subscription.id })),
        screenReader: (subscription) => t('message.subscriptions.action.payments', { id: subscription.id }),
    },
    {
        name: t('action.cancel'),
        icon: XMarkIcon,
        callback: (subscription) => subscriptionBeingCancelled.value = subscription,
        screenReader: (subscription) => t('message.subscriptions.action.cancel', { id: subscription.id }),
        visible: (subscription) => subscription.status === 'Active' || isImmediatelyCancelable(subscription),
    },
];

watch(queryParams, () => useQueryFilter(queryParams).refresh(['subscriptions']));
</script>

<template>
    <ContentLayout :title="t('page.subscriptions')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.subscriptions') }}
                </h1>
            </div>
            <div v-if="page.props.auth.user.is_admin" class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="assigningSubscription = true">
                    <UserPlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.assign') }}
                </PrimaryButton>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div v-if="page.props.auth.user.is_admin">
                        <InputLabel :value="t('entity.user')" for="user" />
                        <ComboboxInput id="user" v-model="queryParams.user"
                                       :options="Object.values(users)"
                                       class="mt-1 block w-full"
                                       value-attribute="id" />
                    </div>
                    <div>
                        <InputLabel :value="t('entity.plan')" for="plan" />
                        <ComboboxInput id="plan" v-model="queryParams.plan"
                                       :options="Object.values(plans)"
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
                </div>
            </div>
        </div>

        <DataTable :actions="actions" :collection="subscriptions" :columns="columns" :only="['subscriptions']"
                   :params="params" />

        <!-- Assign Plan Modal -->
        <DialogModal :show="assigningSubscription" @close="assigningSubscription = false">
            <template #title>
                {{ t('message.subscriptions.assign') }}
            </template>

            <template #content>
                <form id="assign-subscription" @submit.prevent="assignSubscription">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('entity.user')" for="subscription-user" required />
                            <ComboboxInput id="subscription-user" v-model="assignSubscriptionForm.user"
                                           :options="Object.values(users).filter(u => u.email !== page.props.auth.user.email)"
                                           class="mt-1 block w-full"
                                           value-attribute="id" />
                            <InputError :message="assignSubscriptionForm.errors.user" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('entity.plan')" for="subscription-plan" required />
                            <ComboboxInput id="subscription-plan" v-model="assignSubscriptionForm.plan"
                                           :options="Object.values(plans)"
                                           class="mt-1 block w-full"
                                           value-attribute="id" />
                            <InputError :message="assignSubscriptionForm.errors.plan" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.cycles')" for="subscription-cycles" />
                            <ToggleableInput id="subscription-cycles" v-model="assignSubscriptionForm.cycles"
                                             class="mt-1 block w-full" min="1"
                                             type="number" />
                            <InputError :message="assignSubscriptionForm.errors.cycles" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="assigningSubscription = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': assignSubscriptionForm.processing }"
                    :disabled="assignSubscriptionForm.processing"
                    class="ml-3"
                    form="assign-subscription"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Edit Subscription Modal -->
        <DialogModal :show="subscriptionBeingUpdated != null" @close="subscriptionBeingUpdated = null">
            <template #title>
                {{ t('message.subscriptions.edit') }}
            </template>

            <template #content>
                <form id="update-subscription" @submit.prevent="updateSubscription">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.credits')" for="credits" />
                            <ToggleableInput id="credits" v-model="updateSubscriptionForm.features.credits"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="updateSubscriptionForm.errors['features.credits']" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="subscriptionBeingUpdated = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updateSubscriptionForm.processing }"
                    :disabled="updateSubscriptionForm.processing"
                    class="ml-3"
                    form="update-subscription"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Cancel Subscription Confirmation Modal -->
        <ConfirmationModal :show="subscriptionBeingCancelled != null" @close="subscriptionBeingCancelled = null">
            <template #title>
                {{ t('message.subscriptions.cancel') }}
            </template>

            <template #content>
                {{ t('message.subscriptions.cancel_confirmation', { id: subscriptionBeingCancelled.subscription_id }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="subscriptionBeingCancelled = null">
                    {{ t('action.close') }}
                </SecondaryButton>

                <DangerButton
                    v-if="isImmediatelyCancelable(subscriptionBeingCancelled)"
                    :class="{ 'opacity-25': cancelSubcriptionForm.processing }"
                    :disabled="cancelSubcriptionForm.processing"
                    class="ml-3"
                    @click="cancelSubscription(true)"
                >
                    {{ t('action.cancel_immediately') }}
                </DangerButton>

                <DangerButton
                    v-if="subscriptionBeingCancelled.status === 'Active'"
                    :class="{ 'opacity-25': cancelSubcriptionForm.processing }"
                    :disabled="cancelSubcriptionForm.processing"
                    class="ml-3"
                    @click="cancelSubscription(false)"
                >
                    {{ t('action.cancel') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
