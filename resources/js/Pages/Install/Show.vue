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
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Card from '@/Pages/Install/Card.vue';
import { ChevronRightIcon } from '@heroicons/vue/20/solid';
import { router } from '@inertiajs/vue3';
</script>

<template>
    <Card>
        <template #header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.installation.title') }}</h2>
        </template>

        <template #content>
            <div class="flex flex-col justify-center items-center space-y-4">
                <AuthenticationCardLogo />
                <p>{{ $t('message.installation.description') }}</p>
            </div>
        </template>

        <template #footer>
            <PrimaryButton class="w-full justify-center" @click="router.visit(route('install.requirements'))">
                {{ $t('action.start') }}
                <ChevronRightIcon aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" />
            </PrimaryButton>
        </template>
    </Card>
</template>
