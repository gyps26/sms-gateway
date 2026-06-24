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
import List from '@/Pages/Campaigns/List.vue';
import View from '@/Pages/USSD/View.vue';

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    ussdPulls: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
    campaignStatus: {
        type: Object,
        required: false,
    },
});
</script>

<template>
    <List :campaign="campaign" :campaign-status="campaignStatus" :title="$t('page.ussd_pulls')">
        <View :campaign="campaign" :params="params" :users="users" :ussd-pulls="ussdPulls" />
    </List>
</template>
