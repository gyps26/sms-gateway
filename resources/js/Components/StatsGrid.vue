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
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    section: {
        type: String,
        default: null,
    },
    stats: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="m-8">
        <h3 v-if="section" class="text-base font-semibold leading-6 text-gray-900">
            {{ $t(`message.dashboard.section.${ section }`) }}
        </h3>

        <dl class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <template v-for="stat in stats" :key="stat.label">
                <div v-if="stat.visible === undefined || stat.visible"
                     class="relative overflow-hidden rounded-lg bg-white px-4 pb-12 pt-5 shadow sm:px-6 sm:pt-6">
                    <dt>
                        <div :class="[stat.color]" class="absolute rounded-md p-3">
                            <component :is="stat.icon" aria-hidden="true" class="h-6 w-6 text-white" />
                        </div>
                        <p class="ml-16 truncate text-sm font-medium text-gray-500">{{ stat.label }}</p>
                    </dt>
                    <dd class="ml-16 flex items-baseline pb-6 sm:pb-7">
                        <p class="text-2xl font-semibold text-gray-900">{{ stat.value }}</p>
                        <div class="absolute inset-x-0 bottom-0 bg-gray-50 px-4 py-4 sm:px-6">
                            <div class="text-sm">
                                <a v-if="stat.callback"
                                   class="font-medium text-indigo-600 hover:text-indigo-500"
                                   href="#"
                                   @click.prevent="stat.callback">
                                    {{ $t('message.view_all') }} <span class="sr-only"> {{ stat.label }} {{ $t('message.stats') }}</span>
                                </a>
                                <Link v-else :href="stat.route"
                                      class="font-medium text-indigo-600 hover:text-indigo-500">
                                    {{ $t('message.view_all') }}<span class="sr-only"> {{ stat.label }} {{ $t('message.stats') }}</span>
                                </Link>
                            </div>
                        </div>
                    </dd>
                </div>
            </template>
        </dl>
    </div>
</template>
