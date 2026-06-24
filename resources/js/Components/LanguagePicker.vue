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
import { GlobeAltIcon } from '@heroicons/vue/20/solid';
import { router, usePage } from '@inertiajs/vue3';
import { capitalize, computed } from 'vue';

const locale = computed({
    get() {
        return usePage().props.locale.current;
    },
    set(value) {
        router.get(route('language'), { lang: value }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => location.reload(),
        });
    },
});

const options = computed(() =>
    usePage().props.locale.available.map(locale => {
        const languageName = new Intl.DisplayNames([usePage().props.locale.current], {
            type: 'language',
        });

        return {
            value: locale,
            label: capitalize(languageName.of(locale)),
        };
    }),
);
</script>

<template>
    <div v-if="options.length > 1" class="flex items-center justify-center space-x-2 w-44">
        <GlobeAltIcon aria-hidden="true" class="h-10 w-10 text-gray-400" />
        <ComboboxInput v-model="locale" :clearable="false" :options="options" />
    </div>
</template>
