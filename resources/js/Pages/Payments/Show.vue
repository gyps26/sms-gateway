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
import { useCalculator } from '@/Composables/useCalculator.js';
import { useFormatter } from '@/Composables/useFormatter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    CalendarDaysIcon,
    CreditCardIcon,
    DocumentTextIcon,
    ExclamationTriangleIcon,
    IdentificationIcon,
    TicketIcon,
} from '@heroicons/vue/20/solid';

const props = defineProps({
    payment: {
        type: Object,
        required: true,
    },
    instructions: {
        type: String,
        required: true,
    },
});

const {
    taxes,
    discount,
} = useCalculator(
    props.payment.subscription.plan.price,
    props.payment.quantity,
    props.payment.subscription.coupon?.discount ?? 0,
    props.payment.subscription.taxes
)
</script>

<template>
    <ContentLayout :title="$t('message.payment.title', { transactionId: payment.transaction_id})">
        <div v-if="payment.status === 'Pending'" class="border-l-4 border-yellow-400 bg-yellow-50 p-4 m-8">
            <div class="flex">
                <div class="flex-shrink-0">
                    <ExclamationTriangleIcon aria-hidden="true" class="size-8 text-yellow-400" />
                </div>
                <div class="ml-3">
                    <div class="text-yellow-700 space-y-4">
                        <p class="text-lg">{{ $t('message.payment.pending') }}</p>
                        <div class="whitespace-pre-wrap">{{ instructions }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="lg:col-start-3 lg:row-end-1 m-8">
            <h2 class="sr-only">{{ $t('message.payment.summary') }}</h2>
            <div class="rounded-lg bg-gray-50 shadow-sm ring-1 ring-gray-900/5">
                <dl class="flex flex-wrap pb-6">
                    <div class="flex-auto pl-6 pt-6">
                        <dt class="text-sm font-semibold leading-6 text-gray-900">{{ $t('field.amount') }}</dt>
                        <dd class="mt-1 text-base font-semibold leading-6 text-gray-900">
                            {{ useFormatter().formatMoney(payment.amount, payment.currency) }}
                        </dd>
                    </div>
                    <div class="flex-none self-end px-6 pt-4">
                        <dt class="sr-only">{{ $t('field.status') }}</dt>
                        <dd :class="{
                            'bg-green-50 text-green-700 ring-green-600/20' : payment.status === 'Completed',
                            'bg-red-50 text-red-700 ring-red-600/20' : ['Pending', 'Refunded', 'Reversed'].includes(payment.status),
                            'bg-yellow-50 text-yellow-700 ring-yellow-600/20' : payment.status === 'Pending',
                            'bg-gray-50 text-gray-700 ring-gray-600/20' : payment.status === 'Cancelled',
                        }"
                            class="inline-flex items-center rounded-md  px-2 py-1 text-xs font-medium  ring-1 ring-inset ">
                            {{ payment.status }}
                        </dd>
                    </div>
                    <div class="mt-6 flex w-full flex-none gap-x-4 border-t border-gray-900/5 px-6 pt-6">
                        <dt class="flex-none">
                            <span class="sr-only">{{ $t('field.transaction_id') }}</span>
                            <IdentificationIcon aria-hidden="true" class="h-6 w-5 text-gray-400" />
                        </dt>
                        <dd class="text-sm font-medium leading-6 text-gray-900">{{ payment.transaction_id }}</dd>
                    </div>
                    <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                        <dt class="flex-none">
                            <span class="sr-only">{{ $t('field.due_date') }}</span>
                            <CalendarDaysIcon aria-hidden="true" class="h-6 w-5 text-gray-400" />
                        </dt>
                        <dd class="text-sm leading-6 text-gray-500">
                            <time :datetime="new Date(payment.created_at).toISOString()">
                                {{ new Date(payment.created_at).toLocaleString() }}
                            </time>
                        </dd>
                    </div>
                    <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                        <dt class="flex-none">
                            <span class="sr-only">{{ $t('field.method') }}</span>
                            <CreditCardIcon aria-hidden="true" class="h-6 w-5 text-gray-400" />
                        </dt>
                        <dd class="text-sm leading-6 text-gray-500">{{ payment.subscription.payment_method }}</dd>
                    </div>
                    <div class="mt-4 flex w-full flex-none gap-x-4 px-6">
                        <dt class="flex-none">
                            <span class="sr-only">{{ $t('field.discount') }}</span>
                            <TicketIcon aria-hidden="true" class="h-6 w-5 text-gray-400" />
                        </dt>
                        <dd class="text-sm leading-6 text-gray-500">
                            {{ useFormatter().formatMoney(discount, payment.currency) }}
                        </dd>
                    </div>
                    <div v-for="tax in taxes" class="mt-4 flex w-full flex-none gap-x-4 px-6">
                        <dt class="flex-none">
                            <span class="sr-only">{{ tax.name }}</span>
                            <DocumentTextIcon aria-hidden="true" class="h-6 w-5 text-gray-400" />
                        </dt>
                        <dd class="text-sm leading-6 text-gray-500">
                            {{ tax.name }} ({{ tax.rate }}% {{ tax.inclusive ? $t('message.inclusive') : $t('message.exclusive') }}) - {{ useFormatter().formatMoney(tax.amount, payment.currency) }}
                        </dd>
                    </div>
                </dl>
                <div v-if="payment.status === 'Completed'" class="border-t border-gray-900/5 px-6 py-6">
                    <a :href="route('payments.invoice', payment)" class="text-sm font-semibold leading-6 text-gray-900">
                        {{ $t('action.view_invoice') }}
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </div>
    </ContentLayout>
</template>
