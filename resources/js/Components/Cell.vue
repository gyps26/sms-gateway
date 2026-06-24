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
import { CheckIcon, XMarkIcon, MinusIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    value: {
        required: true,
    }
});
</script>

<template>
    <template v-if="typeof value === 'boolean'">
        <CheckIcon v-if="value" aria-hidden="true" class="h-5 w-5 text-green-500" />
        <XMarkIcon v-else aria-hidden="true" class="h-5 w-5 text-red-500" />
    </template>
    <template v-else-if="Array.isArray(value)">
        <ul class="list-disc px-2">
            <li v-for="item in value" :key="item">{{ item }}</li>
        </ul>
    </template>
    <template v-else-if="value !== null && value !== '' && value !== undefined">
        {{ value }}
    </template>
    <template v-else>
        <MinusIcon aria-hidden="true" class="-ml-0.5 mr-2 size-5 text-gray-500" />
    </template>
</template>
