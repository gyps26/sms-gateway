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
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    routeName: {
        type: String,
        required: true,
    },
    responsive: {
        type: Boolean,
        default: false,
    },
});

const active = computed(() => usePage().url.split('?')[0] === (route(props.routeName, [], false) || '/'));
</script>

<template>
    <Link :aria-current="active ? 'page' : undefined"
          :class="[active ? 'bg-cyan-800 text-white' : 'text-cyan-100 hover:text-white hover:bg-cyan-600', responsive ? 'text-base' : 'text-sm leading-6', 'group flex items-center px-2 py-2 rounded-md']"
          :href="route(routeName)"
          preserve-scroll>
        <slot />
    </Link>
</template>
