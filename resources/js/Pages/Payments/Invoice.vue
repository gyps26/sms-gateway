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
import AppHead from "@/Components/AppHead.vue";
import { useCalculator } from '@/Composables/useCalculator.js';
import { useFormatter } from '@/Composables/useFormatter.js';
import { ref } from 'vue';

const props = defineProps({
    payment: {
        type: Object,
        required: true,
    },
    billingInfo: {
        type: String,
        default: null,
    },
});

const invoice = ref(null);

const { grossTotal, taxes, discount } = useCalculator(
    props.payment.subscription.plan.price,
    props.payment.quantity,
    props.payment.subscription.coupon?.discount ?? 0,
    props.payment.subscription.taxes,
);
</script>

<template>
    <AppHead :title="$t('message.invoice.title', { id: payment.id })" />

    <!-- Invoice -->
    <div class="max-w-[85rem] px-4 sm:px-6 lg:px-8 mx-auto my-4 sm:my-10">
        <div class="sm:w-11/12 lg:w-3/4 mx-auto">
            <!-- Card -->
            <div ref="invoice" class="flex flex-col p-4 sm:p-10 bg-white shadow-md rounded-xl dark:bg-neutral-800">
                <!-- Grid -->
                <div class="flex justify-between">
                    <div>
                        <img :alt="$page.props.app.name" :src="$page.props.app.logo" class="size-20" />

                        <h1 class="mt-2 text-lg md:text-xl font-semibold dark:text-white">
                            {{ $page.props.app.name }}
                        </h1>
                    </div>
                    <!-- Col -->

                    <div class="text-end">
                        <h2 class="text-2xl md:text-3xl font-semibold text-gray-800 dark:text-neutral-200">
                            {{ $t('message.invoice.title', { id: payment.id }) }}
                        </h2>

                        <address class="mt-4 not-italic text-gray-800 dark:text-neutral-200" v-html="billingInfo">
                        </address>
                    </div>
                    <!-- Col -->
                </div>
                <!-- End Grid -->

                <!-- Grid -->
                <div class="mt-8 grid sm:grid-cols-2 gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                            {{ $t('field.bill_to') }}:
                        </h3>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                            {{ payment.subscription.billing_info.first_name }}
                            {{ payment.subscription.billing_info.last_name }}
                        </h3>
                        <h3 v-if="payment.subscription.billing_info.company"
                            class="text-base font-semibold text-gray-800 dark:text-neutral-200">
                            {{ payment.subscription.billing_info.company }}
                        </h3>
                        <address class="mt-2 not-italic text-gray-500 dark:text-neutral-500">
                            {{ payment.subscription.billing_info.address_line_1 }}<br>
                            <template v-if="payment.subscription.billing_info.address_line_2">{{ payment.subscription.billing_info.address_line_2 }}<br></template>
                            {{ payment.subscription.billing_info.city }}, {{ payment.subscription.billing_info.state }}<br>
                            {{ payment.subscription.billing_info.country }}, {{ payment.subscription.billing_info.postal_code }}<br>
                            <span v-if="payment.subscription.billing_info.phone">{{ payment.subscription.billing_info.phone }}</span>
                        </address>
                    </div>
                    <!-- Col -->

                    <div class="sm:text-end space-y-2">
                        <!-- Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">
                            <dl class="grid sm:grid-cols-5 gap-x-3">
                                <dt class="col-span-2 font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ $t('field.invoice_date') }}:
                                </dt>
                                <dd class="col-span-3 text-gray-500 dark:text-neutral-500">
                                    {{ (new Date(payment.created_at)).toLocaleString() }}
                                </dd>
                            </dl>
                        </div>
                        <!-- End Grid -->
                    </div>
                    <!-- Col -->
                </div>
                <!-- End Grid -->

                <!-- Table -->
                <div class="mt-6">
                    <div class="border border-gray-200 p-4 rounded-lg space-y-4 dark:border-neutral-700">
                        <div class="hidden sm:grid sm:grid-cols-4">
                            <div
                                class="sm:col-span-2 text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                {{ $t('field.plan') }}
                            </div>
                            <div class="text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                {{ $t('field.duration') }}
                            </div>
                            <div class="text-end text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                {{ $t('field.amount') }}
                            </div>
                        </div>

                        <div class="hidden sm:block border-b border-gray-200 dark:border-neutral-700"></div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div class="col-span-full sm:col-span-2">
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    {{ $t('field.plan') }}
                                </h5>
                                <p class="font-medium text-gray-800 dark:text-neutral-200">
                                    {{ payment.subscription.plan.name }}
                                </p>
                            </div>
                            <div>
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    {{ $t('field.duration') }}
                                </h5>
                                <p class="text-gray-800 dark:text-neutral-200">
                                    {{ payment.subscription.plan.interval * payment.quantity }}
                                    {{ payment.subscription.plan.interval_unit }}</p>
                            </div>
                            <div>
                                <h5 class="sm:hidden text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">
                                    {{ $t('field.amount') }}
                                </h5>
                                <p class="sm:text-end text-gray-800 dark:text-neutral-200">
                                    {{ useFormatter().formatMoney(grossTotal, payment.currency) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Table -->

                <!-- Flex -->
                <div class="mt-8 flex sm:justify-end">
                    <div class="w-full max-w-2xl sm:text-end space-y-2">
                        <!-- Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-1 gap-3 sm:gap-2">

                            <dl class="grid sm:grid-cols-5 gap-x-3">
                                <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ $t('field.method') }}:
                                </dt>
                                <dd class="col-span-2 text-gray-500 dark:text-neutral-500">
                                    {{ payment.subscription.payment_method }}
                                </dd>
                            </dl>

                            <dl class="grid sm:grid-cols-5 gap-x-3">
                                <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ $t('field.discount') }}<span v-if="payment.subscription.coupon?.discount"> ({{ payment.subscription.coupon?.discount }}%)</span>:
                                </dt>
                                <dd class="col-span-2 text-gray-500 dark:text-neutral-500">
                                    {{ useFormatter().formatMoney(discount, payment.currency) }}
                                </dd>
                            </dl>

                            <dl v-for="tax in taxes" class="grid sm:grid-cols-5 gap-x-3">
                                <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ tax.name }} ({{ tax.rate }}% {{ tax.inclusive ? $t('message.inclusive') : $t('message.exclusive') }}):
                                </dt>
                                <dd class="col-span-2 text-gray-500 dark:text-neutral-500">
                                    {{ useFormatter().formatMoney(tax.amount, payment.currency) }}
                                </dd>
                            </dl>

                            <dl class="grid sm:grid-cols-5 gap-x-3">
                                <dt class="col-span-3 font-semibold text-gray-800 dark:text-neutral-200">
                                    {{ $t('field.total') }}:
                                </dt>
                                <dd class="col-span-2 text-gray-500 dark:text-neutral-500">
                                    {{ useFormatter().formatMoney(payment.amount, payment.currency) }}
                                </dd>
                            </dl>
                        </div>
                        <!-- End Grid -->
                    </div>
                </div>
                <!-- End Flex -->

                <div class="mt-8 sm:mt-12">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-neutral-200">
                        {{ $t('message.invoice.thank_you') }}</h4>
                    <p class="text-gray-500 dark:text-neutral-500">{{ $t('message.invoice.footer') }}</p>
                </div>

                <p class="mt-5 text-sm text-gray-500 dark:text-neutral-500">© {{ new Date().getFullYear() }}
                    {{ $page.props.app.name }}.</p>
            </div>
            <!-- End Card -->
        </div>
    </div>
    <!-- End Invoice -->
</template>
