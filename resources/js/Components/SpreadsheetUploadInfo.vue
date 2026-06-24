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
import { useFormatter } from '@/Composables/useFormatter.js';
import { InformationCircleIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    maxUploadSize: {
        type: Number,
        required: true,
    },
    sample: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <div class="rounded-md bg-blue-100">
        <div class="flex">
            <div class="flex-shrink-0">
                <InformationCircleIcon aria-hidden="true"
                                       class="h-5 w-5 text-blue-400" />
            </div>
            <div class="ml-3">
                <div class="text-sm text-blue-700">
                    <p v-html="$t('message.spreadsheet.file', { size: useFormatter().formatBytes(maxUploadSize) })"></p>
                    <p><slot/></p>
                    <p v-html="$t('message.spreadsheet.sample', { sample: `<a href='${sample}' class='underline text-blue-800' download='sample.csv'>sample.csv</a>` })"></p>
                </div>
            </div>
        </div>
    </div>
</template>
