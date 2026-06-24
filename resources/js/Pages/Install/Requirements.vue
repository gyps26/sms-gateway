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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Card from '@/Pages/Install/Card.vue';
import { CheckIcon, XMarkIcon } from '@heroicons/vue/20/solid';
import { ChevronRightIcon } from '@heroicons/vue/20/solid/index.js';
import { router } from '@inertiajs/vue3';

defineProps({
    dependencies: {
        type: Object,
        required: true,

    },
});
</script>

<template>
    <Card>
        <template #header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.installation.requirements') }}</h2>
        </template>

        <template #content>
            <table class="min-w-full divide-y divide-gray-300">
                <tbody class="divide-y divide-gray-200 bg-white">
                <tr v-for="(installed, dependency) in dependencies" :key="dependency">
                    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-900 sm:pl-0">{{ dependency }}</td>
                    <td class="whitespace-nowrap px-2 py-2 text-sm font-medium text-gray-500 flex justify-end">
                        <CheckIcon v-if="installed" class="h-6 w-6 text-green-500" />
                        <XMarkIcon v-else class="h-6 w-6 text-red-500" />
                    </td>
                </tr>
                </tbody>
            </table>
        </template>

        <template #footer>
            <PrimaryButton class="w-full justify-center" @click="router.visit(route('install.permissions'))">
                {{ $t('action.next') }}
                <ChevronRightIcon aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" />
            </PrimaryButton>
        </template>
    </Card>
</template>
