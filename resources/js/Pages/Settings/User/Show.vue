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
import SectionBorder from '@/Components/SectionBorder.vue';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import UpdateDashboardSettings from "@/Pages/Settings/General/Partials/UpdateDashboardSettings.vue";
import UpdateAutoRetrySettings from '@/Pages/Settings/Messaging/Partials/UpdateAutoRetrySettings.vue';
import UpdatePhoneIdSettings from "@/Pages/Settings/Messaging/Partials/UpdatePhoneIdSettings.vue";
import UpdateFeaturesSettings from '@/Pages/Settings/User/Partials/UpdateFeaturesSettings.vue';
import UpdatePromptsSettings from '@/Pages/Settings/Messaging/Partials/UpdatePromptsSettings.vue';
import UpdateQrCodeSettings from "@/Pages/Settings/Misc/Partials/UpdateQrCodeSettings.vue";
import UpdateSimSettings from "@/Pages/Settings/Messaging/Partials/UpdateSimSettings.vue";

const props = defineProps({
    settings: {
        type: Object,
        required: true,
    },
    global: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <ContentLayout :title="$t('page.settings')">
        <template #header>
            <h1 class="font-semibold text-2xl text-gray-800 leading-tight">
                {{ $t('page.user_settings') }}
            </h1>
        </template>

        <div>
            <div class="mx-auto py-10 sm:px-6 lg:px-8">
                <div>
                    <UpdateSimSettings />

                    <SectionBorder />
                </div>

                <div v-if="global.auto_retry.enabled">
                    <UpdateAutoRetrySettings class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div>
                    <UpdateDashboardSettings class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="global.email_to_message || global.message_to_email">
                    <UpdateFeaturesSettings class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div v-if="global.phone_id.enabled">
                    <UpdatePhoneIdSettings class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <div>
                    <UpdatePromptsSettings class="mt-10 sm:mt-0" />

                    <SectionBorder />
                </div>

                <UpdateQrCodeSettings class="mt-10 sm:mt-0" />
            </div>
        </div>
    </ContentLayout>
</template>
