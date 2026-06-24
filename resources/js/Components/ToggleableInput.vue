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
import { omit } from "lodash";
import { computed, nextTick, onMounted, ref } from 'vue';

defineOptions({
    inheritAttrs: false,
});

const props = defineProps({
    modelValue: [String, Number],
});

const emits = defineEmits(['update:modelValue']);

const checkboxInput = ref(null);
const input = ref(null);

let previousValue = '';

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        nextTick(() => {
            if (isChecked.value) {
                input.value.focus();
            } else {
                checkboxInput.value.focus();
            }
        });
    }
});

defineExpose({ focus: () => input.value.focus() });

const proxyModelValue = computed({
    get: () => props.modelValue,
    set: (value) => emits('update:modelValue', value),
});

const isChecked = computed({
    get: () => proxyModelValue.value !== null,
    set: (value) => {
        if (value) {
            proxyModelValue.value = previousValue;
        } else {
            previousValue = proxyModelValue.value;
            proxyModelValue.value = null;
        }
    },
});
</script>

<template>
    <div :class="$attrs.class">
        <div class="grid grid-cols-1 grid-rows-1 mt-1">
            <div
                class="col-span-full row-span-full justify-self-start self-center flex items-center justify-center h-full z-10">
                <input
                    ref="checkboxInput"
                    v-model="isChecked"
                    class="mx-3 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-0 focus:ring-offset-0"
                    type="checkbox"
                >
                <div class="border border-gray-200 h-full"></div>
            </div>
            <div class="col-span-full row-span-full">
                <input
                    ref="input"
                    v-model="proxyModelValue"
                    :disabled="isChecked === false"
                    class="w-full pl-14 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                    v-bind="omit($attrs, ['class'])"
                >
            </div>
        </div>
    </div>
</template>
