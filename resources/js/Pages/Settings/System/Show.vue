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
import SectionBorder from "@/Components/SectionBorder.vue";
import Layout from "@/Pages/Settings/Layout.vue";
import CronConfiguration from "@/Pages/Settings/System/Partials/CronConfiguration.vue";
import UpdateLicenseSettings from "@/Pages/Settings/System/Partials/UpdateLicenseSettings.vue";
import UpdateSystemSettings from "@/Pages/Settings/System/Partials/UpdateSystemSettings.vue";

const props = defineProps({
    version: {
        type: String,
        required: true,
    },
    licenseCode: {
        type: String,
        required: true,
    },
    queueUp: {
        type: Boolean,
        required: true,
    },
    maxUploadSize: {
        type: Number,
        required: true,
    },
    cron: {
        type: Object,
        required: true,
    }
});


</script>

<template>
    <Layout :title="$t('page.system')">
        <div>
            <div class="mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <UpdateSystemSettings />

                    <SectionBorder />
                </div>

                <div>
                    <CronConfiguration class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <UpdateLicenseSettings class="mt-10 sm:mt-0" />
            </div>
        </div>
    </Layout>
</template>
