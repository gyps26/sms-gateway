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
const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: false,
    },
});
</script>

<template>
    <div class="px-4 pb-4 sm:px-6 lg:px-8">
        <div class="space-y-8 divide-y divide-gray-200">
            <div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">
                <div class="space-y-6 sm:space-y-5">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            {{ title }}
                        </h3>
                        <p v-if="description" class="mt-1 max-w-2xl text-sm text-gray-500">
                            {{ description }}
                        </p>
                    </div>

                    <div class="space-y-6 sm:space-y-5">
                        <slot />
                    </div>
                </div>
            </div>

            <div class="pt-5">
                <div class="flex justify-end">
                    <slot name="actions" />
                </div>
            </div>
        </div>
    </div>
</template>
