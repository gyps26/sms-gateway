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
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Component :is="href ? Link : 'span'"
               :aria-current="active ? 'page' : undefined"
               :class="[active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : `bg-white border-gray-300 text-gray-500 ${ href ? 'hover:bg-gray-50' : 'text-gray-400 cursor-default select-none' }`, 'relative inline-flex items-center px-4 py-2 border text-sm font-medium']"
               :href="href"
               preserve-scroll
               preserve-state>
        <slot/>
    </Component>
</template>
