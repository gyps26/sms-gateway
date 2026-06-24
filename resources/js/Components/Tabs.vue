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
import ComboboxInput from '@/Components/ComboboxInput.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    tabs: {
        type: Array,
        required: true,
    },
});

const current = computed({
    get() {
        return props.tabs[props.tabs.findIndex((tab) => tab.current)]?.href;
    },

    set(val) {
        router.visit(val, {
            preserveScroll: true,
            preserveState: true,
        });
    },
});
</script>

<template>
    <div>
        <div class="sm:hidden">
            <label class="sr-only" for="tabs">{{ $t('message.tabs.select') }}</label>
            <ComboboxInput id="tabs"
                           v-model="current" :clearable="false" :hideSearchBox="true" :options="tabs.filter(tab => tab.visible === undefined || tab.visible)"
                           textAttribute="name"
                           valueAttribute="href" />
        </div>
        <div class="hidden sm:block">
            <div class="border-b border-gray-200">
                <nav aria-label="Tabs" class="-mb-px flex space-x-8">
                    <template v-for="tab in tabs" :key="tab.name">
                        <Link v-if="tab.visible === undefined || tab.visible"
                              :aria-current="tab.current ? 'page' : undefined"
                              :class="[tab.current ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm']"
                              :href="tab.href">
                            <component :is="tab.icon" v-if="tab.icon"
                                       :class="[tab.current ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500', '-ml-0.5 mr-2 h-5 w-5']"
                                       aria-hidden="true" />
                            <span>{{ tab.name }}</span>
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>
