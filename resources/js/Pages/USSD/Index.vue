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
import LinkButton from '@/Components/LinkButton.vue';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import View from '@/Pages/USSD/View.vue';
import { PaperAirplaneIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    ussdPulls: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <ContentLayout :title="$t('page.ussd_pulls')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $t('page.ussd_pulls') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <LinkButton :href="route('ussd-pulls.create')">
                    <PaperAirplaneIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ $t('action.send') }}
                </LinkButton>
            </div>
        </template>

        <View :params="params" :users="users" :ussd-pulls="ussdPulls"></View>
    </ContentLayout>
</template>
