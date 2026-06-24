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
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Radio from '@/Components/Radio.vue';
import TextInput from '@/Components/TextInput.vue';
import { useCalculator } from '@/Composables/useCalculator.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { CheckIcon, ShoppingBagIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { Link, useForm } from '@inertiajs/vue3';
import { kebabCase } from 'lodash';
import { computed, ref } from 'vue';
import { useFormatter } from '../Composables/useFormatter.js';

const props = defineProps({
    plan: {
        type: Object,
        required: true,
    },
    countries: {
        type: Array,
        required: true,
    },
    taxes: {
        type: Array,
        required: true,
    },
    paymentMethods: {
        type: Array,
        required: true,
    },
});

const checkoutForm = useForm({
    first_name: null,
    last_name: null,
    company: null,
    address_line_1: null,
    address_line_2: null,
    city: null,
    country: null,
    state: null,
    postal_code: null,
    phone: null,
    payment_method: props.paymentMethods[0] ?? null,
    coupon: null,
    quantity: 1,
});

const applyCouponForm = useForm({
    coupon: null,
});

const discount = ref(0);

const taxes = computed(() => props.taxes.filter((tax) => tax.country === checkoutForm.country));
const calculator = computed(() => useCalculator(
    props.plan.price,
    checkoutForm.quantity,
    discount.value,
    taxes.value,
));

const checkout = () => {
    checkoutForm.post(route('plans.subscriptions.store', props.plan), {
        preserveScroll: true,
        preserveState: true,
    });
};

const applyCoupon = () => {
    applyCouponForm.processing = true;
    axios.post(route('coupons.apply'), applyCouponForm.data(), {
        headers: {
            'Content-Type': 'application/json',
        },
    }).then(response => {
        checkoutForm.coupon = applyCouponForm.coupon;
        discount.value = response.data.discount;
        applyCouponForm.clearErrors();
    }).catch(error => {
        applyCouponForm.setError('coupon', error.response.data.message);
    }).finally(() => {
        applyCouponForm.processing = false;
    });
};
</script>

<template>
    <ContentLayout :title="$t('page.checkout')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $t('page.checkout') }}
                </h1>
            </div>
        </template>

        <div>
            <div class="mx-auto px-4 pb-24 sm:px-6 lg:px-8">
                <h2 class="sr-only">{{ $t('page.checkout') }}</h2>

                <form class="lg:grid lg:grid-cols-2 lg:gap-x-12 xl:gap-x-16" @submit.prevent="checkout">
                    <div>
                        <div>
                            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.checkout.payment') }}</h2>

                            <fieldset class="mt-4">
                                <legend class="sr-only">{{ $t('field.payment_method') }} <span
                                    class="text-red-600">*</span></legend>
                                <div class="space-y-4 flex flex-col items-start">
                                    <div v-for="paymentMethod in paymentMethods" :key="paymentMethod"
                                         class="flex items-center">
                                        <Radio :id="kebabCase(paymentMethod)" v-model="checkoutForm.payment_method"
                                               :autofocus="paymentMethods[0] === paymentMethod"
                                               :value="paymentMethod"
                                               name="payment-method"
                                               required />
                                        <InputLabel :for="kebabCase(paymentMethod)" :value="paymentMethod"
                                                    class="ml-3" />
                                    </div>
                                    <InputError :message="checkoutForm.errors.payment_method" class="mt-2" />
                                </div>
                            </fieldset>
                        </div>

                        <!-- Payment -->
                        <div class="mt-10 border-t border-gray-200 pt-10">
                            <h2 class="text-lg font-medium text-gray-900">{{ $t('field.billing_information') }}</h2>

                            <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                                <div>
                                    <InputLabel :value="$t('field.first_name')" for="first-name" required />
                                    <TextInput id="first-name" v-model="checkoutForm.first_name"
                                               class="mt-1 block w-full" required />
                                    <InputError :message="checkoutForm.errors.first_name" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="$t('field.last_name')" for="last-name" required />
                                    <TextInput id="last-name" v-model="checkoutForm.last_name"
                                               class="mt-1 block w-full" required />
                                    <InputError :message="checkoutForm.errors.last_name" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel :value="$t('field.company')" for="company" />
                                    <TextInput id="company" v-model="checkoutForm.company" class="mt-1 block w-full" />
                                    <InputError :message="checkoutForm.errors.company" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel :value="$t('field.address_line_1')" for="address-line-1" required />
                                    <TextInput id="address-line-1" v-model="checkoutForm.address_line_1"
                                               class="mt-1 block w-full"
                                               required />
                                    <InputError :message="checkoutForm.errors.address_line_1" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel :value="$t('field.address_line_2')" for="address-line-2" />
                                    <TextInput id="address-line-2" v-model="checkoutForm.address_line_2"
                                               class="mt-1 block w-full" />
                                    <InputError :message="checkoutForm.errors.address_line_2" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="$t('field.city')" for="city" required />
                                    <TextInput id="city" v-model="checkoutForm.city" class="mt-1 block w-full"
                                               required />
                                    <InputError :message="checkoutForm.errors.city" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="$t('field.country')" for="country" required />
                                    <ComboboxInput id="country" v-model="checkoutForm.country" :options="countries"
                                                   class="mt-1 block w-full" required />
                                    <InputError :message="checkoutForm.errors.country" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="$t('field.state')" for="state" required />
                                    <TextInput id="state" v-model="checkoutForm.state" class="mt-1 block w-full"
                                               required />
                                    <InputError :message="checkoutForm.errors.state" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel :value="$t('field.postal_code')" for="postal-code" required />
                                    <TextInput id="postal-code" v-model="checkoutForm.postal_code"
                                               class="mt-1 block w-full" required />
                                    <InputError :message="checkoutForm.errors.postal_code" class="mt-2" />
                                </div>

                                <div class="sm:col-span-2">
                                    <InputLabel :value="$t('field.phone_number')" for="phone" />
                                    <TextInput id="phone" v-model="checkoutForm.phone" class="mt-1 block w-full"
                                               type="tel" />
                                    <InputError :message="checkoutForm.errors.phone" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order summary -->
                    <div class="mt-10 lg:mt-0">
                        <h2 class="text-lg font-medium text-gray-900">{{ $t('message.checkout.order') }}</h2>

                        <div class="mt-4 rounded-lg border border-gray-200 bg-white shadow-sm">
                            <h3 class="sr-only">{{ $t('message.checkout.items') }}</h3>
                            <div class="py-6 px-4 sm:px-6">
                                <div class="flex flex-1 flex-col">
                                    <div class="flex">
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-lg font-bold">
                                                {{ plan.name }}
                                            </h4>
                                            <p class="mt-1 text-sm text-gray-500">{{ plan.description }}</p>
                                        </div>

                                        <div class="ml-4 flow-root flex-shrink-0">
                                            <button
                                                class="-m-2.5 flex items-center justify-center bg-white p-2.5 text-gray-400 hover:text-gray-500"
                                                type="button">
                                                <span class="sr-only">{{ $t('action.remove') }}</span>
                                                <Link :href="route('subscribe')">
                                                    <TrashIcon aria-hidden="true" class="h-5 w-5" />
                                                </Link>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 items-center justify-between pt-2">
                                        <p class="mt-1 text-sm font-medium text-gray-900">
                                            {{ useFormatter().formatMoney(plan.price, plan.currency) }}
                                        </p>

                                        <div v-if="checkoutForm.payment_method ===  'Bank Transfer'" class="ml-4">
                                            <InputLabel class="sr-only" for="quantity" value="Quantity" />
                                            <TextInput id="quantity" v-model="checkoutForm.quantity" class="w-20"
                                                       min="1" type="number" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                                <InputLabel :value="$t('message.checkout.coupon')" for="coupon" />
                                <div class="mt-3 space-x-4 flex items-start">
                                    <div class="flex-1">
                                        <TextInput id="coupon" v-model="applyCouponForm.coupon" class="w-full" />
                                        <InputError :message="applyCouponForm.errors.coupon" class="mt-2" />
                                    </div>
                                    <DangerButton v-if="checkoutForm.coupon"
                                                  @click="checkoutForm.coupon = null; discount = 0">
                                        <TrashIcon aria-hidden="true" class="-ml-0.5 mr-2 h-6 w-6" />
                                        {{ $t('action.clear') }}
                                    </DangerButton>
                                    <PrimaryButton v-else
                                                   :class="{ 'opacity-25': applyCouponForm.processing }"
                                                   :disabled="applyCouponForm.processing || applyCouponForm.coupon === null || applyCouponForm.coupon === ''"
                                                   type="button"
                                                   @click="applyCoupon">
                                        <CheckIcon aria-hidden="true" class="-ml-0.5 mr-2 h-6 w-6" />
                                        {{ $t('action.apply') }}
                                    </PrimaryButton>
                                </div>
                            </div>
                            <dl class="space-y-6 border-t border-gray-200 px-4 py-6 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <dt class="text-sm">{{ $t('message.checkout.sub_total') }}</dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ useFormatter().formatMoney(calculator.grossTotal, plan.currency) }}
                                    </dd>
                                </div>
                                <div v-for="tax in calculator.taxes" class="flex items-center justify-between">
                                    <dt class="text-sm">
                                        {{ tax.name }} ({{ tax.rate }}% {{ tax.inclusive ? $t('message.inclusive') : $t('message.exclusive') }})
                                    </dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ useFormatter().formatMoney(tax.amount, plan.currency) }}
                                    </dd>
                                </div>
                                <div v-if="discount" class="flex items-center justify-between">
                                    <dt class="text-sm">
                                        {{ $t('message.checkout.discount', { rate: discount }) }}
                                    </dt>
                                    <dd class="text-sm font-medium text-gray-900">
                                        {{ useFormatter().formatMoney(calculator.discount, plan.currency) }}
                                    </dd>
                                </div>
                                <div class="flex items-center justify-between border-t border-gray-200 pt-6">
                                    <dt class="text-base font-medium">{{ $t('message.checkout.total') }}</dt>
                                    <dd class="text-base font-medium text-gray-900">
                                        {{ useFormatter().formatMoney(calculator.netTotal, plan.currency) }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="border-t border-gray-200 px-4 py-6 sm:px-6 flex justify-end">
                                <PrimaryButton
                                    :class="{ 'opacity-25': checkoutForm.processing }"
                                    :disabled="checkoutForm.processing">
                                    <ShoppingBagIcon aria-hidden="true" class="-ml-0.5 mr-2 h-6 w-6" />
                                    {{ $t('message.checkout.confirm_order') }}
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </ContentLayout>
</template>
