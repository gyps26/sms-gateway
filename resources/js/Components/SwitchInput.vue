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
import { Switch, SwitchGroup, SwitchLabel } from '@headlessui/vue';
import { computed } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    label: {
        type: String,
        default: null,
    },
    modelValue: {
        type: Boolean,
        required: true,
    },
});

const enabled = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit('update:modelValue', val);
    },
});
</script>

<template>
    <SwitchGroup as="div" class="flex items-center">
        <Switch v-model="enabled"
                :class="[enabled ? 'bg-indigo-600' : 'bg-gray-200', 'relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring focus:ring-offset-2 focus:ring-opacity-50 focus:ring-indigo-200']">
            <span class="sr-only">{{ $t('message.switch.toggle') }}</span>
            <span
                :class="[enabled ? 'translate-x-5' : 'translate-x-0', 'pointer-events-none relative inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200']">
                <span
                    :class="[enabled ? 'opacity-0 ease-out duration-100' : 'opacity-100 ease-in duration-200', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
                    aria-hidden="true">
                    <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                        <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" />
                    </svg>
                </span>
                <span
                    :class="[enabled ? 'opacity-100 ease-in duration-200' : 'opacity-0 ease-out duration-100', 'absolute inset-0 h-full w-full flex items-center justify-center transition-opacity']"
                    aria-hidden="true">
                    <svg class="h-3 w-3 text-indigo-600" fill="currentColor" viewBox="0 0 12 12">
                        <path
                            d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                    </svg>
                </span>
            </span>
        </Switch>
        <SwitchLabel v-if="label" as="span" class="ml-3">
            <span class="text-sm font-medium text-gray-900">{{ label }}</span>
        </SwitchLabel>
    </SwitchGroup>
</template>
