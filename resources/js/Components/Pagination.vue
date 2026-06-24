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
import { ChevronLeftIcon, ChevronRightIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    links: {
        type: Object,
        required: true,
    },
    meta: {
        type: Object,
        required: true,
    },
    only: {
        type: Array,
        default: [],
    }
});
</script>

<template>
    <div v-if="meta.total > 0"
         class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between md:hidden">
            <PaginationLink :href="links.prev" class="rounded-md text-gray-700">
                {{ $t('action.previous') }}
            </PaginationLink>
            <PaginationLink :href="links.next" class="ml-3 rounded-md text-gray-700">
                {{ $t('action.next') }}
            </PaginationLink>
        </div>
        <div class="w-full hidden md:flex-1 md:flex md:items-center md:justify-between gap-1">
            <div>
                <p class="text-sm text-gray-700"
                   v-html="$t('message.pagination', { from: meta.from, to: meta.to, total: meta.total })"></p>
            </div>
            <div class="overflow-auto">
                <nav aria-label="Pagination" class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                    <PaginationLink :href="links.prev" :only="only" class="px-2 rounded-l-md">
                        <span class="sr-only">{{ $t('action.previous') }}</span>
                        <ChevronLeftIcon aria-hidden="true" class="h-5 w-5" />
                    </PaginationLink>
                    <PaginationLink v-for="link in meta.links.slice(1, -1)" :key="link.url" :active="link.active"
                                    :href="link.url" :only="only">
                        {{ link.label }}
                    </PaginationLink>
                    <PaginationLink :href="links.next" :only="only" class="px-2 rounded-r-md">
                        <span class="sr-only">{{ $t('action.next') }}</span>
                        <ChevronRightIcon aria-hidden="true" class="h-5 w-5" />
                    </PaginationLink>
                </nav>
            </div>
        </div>
    </div>
</template>
