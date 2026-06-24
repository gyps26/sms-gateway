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
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

const emit = defineEmits(['update:after', 'update:before']);

const props = defineProps({
    after: {
        type: String,
        default: null,
    },
    before: {
        type: String,
        default: null,
    },
});

const proxyAfter = computed({
    get() {
        return props.after;
    },
    set(value) {
        emit('update:after', value);
    },
});


const proxyBefore = computed({
    get() {
        return props.before;
    },
    set(value) {
        emit('update:before', value);
    },
});
</script>

<template>
    <div>
        <InputLabel :value="$t('field.after')" for="after" />
        <TextInput id="after" v-model="proxyAfter" :max="proxyBefore" class="mt-1 block w-full" type="date" />
    </div>
    <div>
        <InputLabel :value="$t('field.before')" for="before" />
        <TextInput id="before" v-model="proxyBefore" :min="proxyAfter" class="mt-1 block w-full" type="date" />
    </div>
</template>
