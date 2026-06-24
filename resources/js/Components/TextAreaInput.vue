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
import { computed, nextTick, onMounted, ref } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: String,
        default: null,
    },
});

const textarea = ref(null);

const proxyModelValue = computed({
    get() {
        return props.modelValue;
    },

    set(val) {
        emit('update:modelValue', val);
    },
});

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        nextTick(() => textarea.value.focus());
    }
});

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea ref="textarea" v-model="proxyModelValue"
              class="shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full border-gray-300 rounded-md"
              rows="4" />
</template>
