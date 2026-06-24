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
import Tabs from '@/Components/Tabs.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { ArrowUpOnSquareStackIcon, CubeTransparentIcon } from '@heroicons/vue/20/solid';

const { t } = useI18n();

const tabs = [
    {
        name: t('page.webhooks'),
        icon: CubeTransparentIcon,
        href: route('webhooks.index'),
        current: route().current('webhooks.index'),
    },
    {
        name: t('page.webhook_calls'),
        icon: ArrowUpOnSquareStackIcon,
        href: route('webhook-calls.index'),
        current: route().current('webhook-calls.index'),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.webhooks')">
        <template #header>
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ t('page.webhooks') }}
                    </h1>
                </div>
            </div>
        </template>

        <div class="px-4 sm:px-6 lg:mx-auto lg:px-8">
            <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                <div class="md:flex md:items-center md:justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Webhooks</h3>
                    <div class="mt-3 flex md:absolute md:top-3 md:right-0 md:mt-0">
                        <slot name="actions" />
                    </div>
                </div>
                <div class="mt-4">
                    <Tabs :tabs="tabs"></Tabs>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <slot />
        </div>
    </ContentLayout>
</template>
