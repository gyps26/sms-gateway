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
import PaginationLink from '@/Components/PaginationLink.vue';

const props = defineProps({
    links: {
        type: Object,
        required: true,
    },
    meta: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <nav v-if="meta.total > 0"
         aria-label="Pagination"
         class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="hidden sm:block">
            <p class="text-sm text-gray-700"
               v-html="$t('message.pagination', { from: meta.from, to: meta.to, total: meta.total })"></p>
        </div>
        <div class="flex-1 flex justify-between sm:justify-end">
            <PaginationLink :href="links.prev" class="text-gray-700 rounded-md">{{ $t('action.previous') }}</PaginationLink>
            <PaginationLink :href="links.next" class="ml-3 text-gray-700 rounded-md">{{ $t('action.next') }}</PaginationLink>
        </div>
    </nav>
</template>
