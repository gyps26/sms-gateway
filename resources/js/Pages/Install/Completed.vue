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
import CopyInput from '@/Components/CopyInput.vue';
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Card from '@/Pages/Install/Card.vue';
import { ChevronRightIcon } from '@heroicons/vue/20/solid';
import { router } from '@inertiajs/vue3';

defineProps({
    command: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Card>
        <template #header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.installation.completed') }}</h2>
        </template>

        <template #content>
            <InputLabel :value="$t('message.installation.cron_instructions')" for="cron-command" />
            <CopyInput id="cron-command" :content="command" class="mt-1 block w-full" />
        </template>

        <template #footer>
            <PrimaryButton class="w-full justify-center" @click="router.visit(route('login'))">
                {{ $t('action.login') }}
                <ChevronRightIcon aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" />
            </PrimaryButton>
        </template>
    </Card>
</template>
