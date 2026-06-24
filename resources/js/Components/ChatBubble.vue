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
import Attachments from "@/Components/Attachments.vue";
import {
    ArrowPathRoundedSquareIcon,
    CheckBadgeIcon,
    CheckIcon,
    ClockIcon,
    RectangleStackIcon,
    ExclamationTriangleIcon
} from "@heroicons/vue/20/solid";

const props = defineProps({
    message: {
        type: Object,
        required: true,
    }
});

const getTime = (date) => {
    return new Intl.DateTimeFormat(
        'en-US',
        { hour: 'numeric', minute: 'numeric' }
    ).format(new Date(date));
};
</script>

<template>
    <div :class="[
        'flex',
        { 'justify-start': message.status === 'Received' },
        { 'justify-end': message.status !== 'Received' }
    ]">
        <div :class="[
            'flex flex-none space-x-4 px-4 py-2 lg:max-w-md max-w-xs',
            'rounded-br-md rounded-bl-md',
            { 'bg-gray-200 text-gray-800 rounded-tr-md': message.status === 'Received' },
            { 'bg-blue-500 text-white rounded-tl-md': message.status !== 'Received' }
        ]">
            <div class="flex-1 flex flex-col space-y-4">
                <span v-if="message.content" class="text-base">{{ message.content }}</span>
                <Attachments :attachments="message.attachments" />
            </div>
            <span class="flex flex-col justify-end text-xs font-bold">
                <span class="flex items-center gap-0.5">
                    <span v-if="message.status === 'Received'">{{ getTime(message.delivered_at) }}</span>
                    <span v-else>{{ getTime(message.sent_at) }}</span>
                    <ClockIcon v-if="message.status === 'Pending'" class="inline size-3.5" />
                    <RectangleStackIcon v-if="message.status === 'Queued'" class="inline size-4" />
                    <ArrowPathRoundedSquareIcon v-if="message.status === 'Processed'" class="inline size-4" />
                    <ExclamationTriangleIcon v-if="message.status === 'Failed'" class="inline size-4" />
                    <CheckIcon v-if="message.status === 'Sent'" class="inline size-4" />
                    <CheckBadgeIcon v-else-if="message.status === 'Delivered'" class="inline size-4" />
                </span>
            </span>
        </div>
    </div>
</template>d