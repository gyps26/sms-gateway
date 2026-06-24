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
import AppHead from "@/Components/AppHead.vue";
import LanguagePicker from '@/Components/LanguagePicker.vue';
</script>

<template>
    <AppHead :title="$t('message.installation.title')" />

    <div class="min-h-screen flex flex-col">
        <div class="flex justify-end m-4">
            <LanguagePicker />
        </div>

        <div class="flex-1 flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="w-full sm:max-w-xl divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow">
                <div class="px-4 py-5 sm:px-6">
                    <slot name="header" />
                </div>
                <div class="px-4 py-5 sm:p-6">
                    <slot name="content" />
                </div>
                <div class="px-4 py-4 sm:px-6">
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </div>
</template>
