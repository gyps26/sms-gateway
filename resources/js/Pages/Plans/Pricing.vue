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
import ConfirmationModal from "@/Components/ConfirmationModal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useFormatter } from "@/Composables/useFormatter.js";
import { useI18n } from "@/Composables/useI18n.js";
import { CheckIcon, MinusIcon } from "@heroicons/vue/20/solid/index.js";
import { router, useForm } from "@inertiajs/vue3";
import { every } from "lodash";
import { ref } from "vue";

const props = defineProps({
    plans: {
        type: Array,
        required: true,
    },
    subscription: {
        type: Object,
        default: null,
    },
});

const { t } = useI18n();

const cancelingSubscription = ref(false);
const downgradingSubscription = ref(null);

const cancelSubscriptionForm = useForm({});

const features = [
    { name: t('field.credits'), field: 'credits' },
    { name: t('field.contacts'), field: 'contacts' },
    { name: t('field.contact_lists'), field: 'contact_lists' },
    { name: t('field.devices'), field: 'devices' },
    { name: t('field.sending_servers'), field: 'sending_servers' },
    { name: t('field.sender_ids'), field: 'sender_ids' },
    { name: t('field.api_tokens'), field: 'api_tokens' },
    { name: t('field.templates'), field: 'templates' },
    { name: t('field.webhooks'), field: 'webhooks' },
    { name: t('field.data_export'), field: 'data_export' },
];

const checkout = (plan) => {
    const result = every(plan.criteria, (value) => value === 0);

    if (result) {
        router.visit(route('plans.subscriptions.checkout', plan));
        return;
    }

    downgradingSubscription.value = plan;
};

const cancelSubscription = () => {
    cancelSubscriptionForm.post(route('subscriptions.cancel', props.subscription), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => cancelingSubscription.value = false,
    });
};
</script>

<template>
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-4xl text-center">
            <h2 class="text-base font-semibold leading-7 text-indigo-600">
                {{ t('message.pricing.title') }}
            </h2>
            <p class="mt-2 text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                {{ t('message.pricing.tagline') }}
            </p>
        </div>
        <p class="mx-auto mt-6 max-w-2xl text-center text-lg leading-8 text-gray-600">
            {{ t('message.pricing.description') }}
        </p>
        <div
            class="isolate mx-auto mt-20 grid max-w-md grid-cols-1 gap-8 md:max-w-2xl md:grid-cols-2 lg:max-w-4xl xl:mx-0 xl:max-w-none xl:grid-cols-4">
            <div v-for="plan in plans" :key="plan.id"
                 :class="[plans[0] === plan ? 'ring-2 ring-indigo-600' : 'ring-1 ring-gray-200', 'rounded-3xl p-8']">
                <h3 :id="plan.id"
                    :class="[plans[0] === plan ? 'text-indigo-600' : 'text-gray-900', 'text-lg font-semibold leading-8']">
                    {{ plan.name }}
                </h3>
                <p class="mt-4 text-sm leading-6 text-gray-600">{{ plan.description }}</p>
                <p class="mt-6 flex items-baseline gap-x-1">
                    <span class="text-xl font-bold tracking-tight text-gray-900">
                        {{ useFormatter().formatMoney(plan.price, plan.currency) }}
                    </span>
                    <span class="text-sm font-semibold leading-6 text-gray-600">
                        / {{ plan.interval }} {{ plan.interval_unit }}
                    </span>
                </p>
                <DangerButton
                    v-if="subscription && subscription.status === 'Active' && subscription.plan_id === plan.id"
                    :aria-describedby="plan.id"
                    class="w-full leading-6 mt-6 text-center"
                    @click.prevent="cancelingSubscription = true">
                    {{ t('action.cancel') }}
                </DangerButton>
                <PrimaryButton v-else :aria-describedby="plan.id" :disabled="subscription?.status === 'Active'"
                               class="w-full leading-6 mt-6 justify-center" @click.prevent="checkout(plan)">
                    {{ t('action.buy_now') }}
                </PrimaryButton>
                <ul class="mt-8 space-y-3 text-sm leading-6 text-gray-600" role="list">
                    <li v-for="feature in features" :key="feature.field" class="flex gap-x-3">
                        <MinusIcon
                            v-if="plan.features[feature.field] === false || plan.features[feature.field] === 0"
                            aria-hidden="true" class="h-6 w-5 flex-none text-red-600" />
                        <CheckIcon v-else aria-hidden="true" class="h-6 w-5 flex-none text-green-600" />
                        <template v-if="typeof plan.features[feature.field] === 'boolean'"></template>
                        <template v-else-if="plan.features[feature.field] === null">
                            {{ t('message.pricing.unlimited') }}
                        </template>
                        <template v-else>
                            {{ plan.features[feature.field] }}
                        </template>
                        {{ feature.name }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Cancel Subscription Confirmation Modal -->
    <ConfirmationModal :show="cancelingSubscription" @close="cancelingSubscription = false">
        <template #title>
            {{ t('message.pricing.cancel') }}
        </template>

        <template #content>
            {{ t('message.pricing.cancel_confirmation') }}
        </template>

        <template #footer>
            <SecondaryButton @click="cancelingSubscription = false">
                {{ t('action.no') }}
            </SecondaryButton>

            <PrimaryButton
                :class="{ 'opacity-25': cancelSubscriptionForm.processing }"
                :disabled="cancelSubscriptionForm.processing"
                class="ml-3"
                @click="cancelSubscription"
            >
                {{ t('action.yes') }}
            </PrimaryButton>
        </template>
    </ConfirmationModal>

    <!-- Downgrade Subscription Modal -->
    <DialogModal :show="downgradingSubscription != null" @close="downgradingSubscription = null">
        <template #title>
            {{ t('message.pricing.downgrade') }}
        </template>

        <template #content>
            <p>{{ t('message.pricing.downgrade_warning') }}</p>
            <table class="min-w-full divide-y divide-gray-300">
                <thead>
                <tr>
                    <th class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0"
                        scope="col">
                        {{ t('field.feature') }}
                    </th>
                    <th class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900"
                        scope="col">
                        {{ t('field.additional') }}
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                <template v-for="(value, key) in downgradingSubscription.criteria" :key="key">
                    <tr v-if="value > 0">
                        <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-900 sm:pl-0">
                            {{ t(`field.${ key }`) }}
                        </td>
                        <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-500">{{ value }}</td>
                    </tr>
                </template>
                </tbody>
            </table>
        </template>

        <template #footer>
            <PrimaryButton @click="downgradingSubscription = null">
                {{ t('action.close') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
