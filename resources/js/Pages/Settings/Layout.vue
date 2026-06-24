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
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();

const navigation = [
    { name: t('page.general'), route: 'settings.general.edit' },
    { name: t('page.mail'), route: 'settings.mail.edit' },
    { name: t('page.messaging'), route: 'settings.messaging.edit' },
    { name: t('page.misc'), route: 'settings.misc.edit' },
    { name: t('page.payment_gateway'), route: 'settings.payment-gateway.edit' },
    { name: t('page.saas'), route: 'settings.saas.edit' },
    { name: t('page.system'), route: 'settings.system.edit' },
];

const current = computed({
    get() {
        return route().current();
    },

    set(val) {
        router.visit(route(val), {
            preserveScroll: true,
            preserveState: true,
        });
    },
});
</script>

<template>
    <ContentLayout :title="`${ title } ${ t('page.settings') }`">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.settings') }}
                </h1>
            </div>
            <div class="hidden mt-6 space-x-3 md:block md:mt-0">
                <div class="space-x-2">
                    <Link v-for="item in navigation" :key="item.name" :aria-current="item.current ? 'page' : undefined"
                          :class="[route().current(item.route) ? 'bg-gray-100 text-gray-900' : 'text-gray-900 hover:bg-gray-50 hover:text-gray-900', 'inline-flex items-center rounded-md py-2 px-3 text-sm font-medium']"
                          :href="route(item.route)">
                        {{ item.name }}
                    </Link>
                </div>
            </div>
            <div class="md:hidden mt-6">
                <label class="sr-only" for="tabs">{{ $t('message.tabs.select') }}</label>
                <ComboboxInput id="tabs"
                               v-model="current"
                               :clearable="false"
                               :hideSearchBox="true"
                               :options="navigation"
                               text-attribute="name"
                               value-attribute="route" />
            </div>
        </template>

        <slot />
    </ContentLayout>
</template>
