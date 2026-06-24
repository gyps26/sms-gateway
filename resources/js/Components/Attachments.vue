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
import { ArrowDownTrayIcon } from "@heroicons/vue/20/solid";

const props = defineProps({
    attachments: {
        type: Array,
        required: true,
    }
});

const audioTypes = ['audio/aac', 'audio/mpeg', 'audio/ogg', 'audio/wav', 'audio/webm', 'audio/flac', 'audio/x-flac', 'audio/mp4', 'audio/wave', 'audio/x-wav', 'audio/x-pn-wav'];
const videoTypes = ['video/3gpp', 'video/3gpp2', 'video/3gp2', 'video/mp4', 'video/mpeg', 'video/ogg', 'video/webm'];
const imageTypes = ['image/apng', 'image/avif', 'image/gif', 'image/jpeg', 'image/png', 'image/svg+xml', 'image/webp'];
</script>

<template>
    <div v-for="attachment in attachments" :key="attachment.id">
        <a v-if="imageTypes.includes(attachment.mime_type)"
           :href="attachment.url"
           download>
            <img :alt="attachment.name"
                 :src="attachment.url"
                 class="size-40 object-cover rounded-md shadow-sm" />
        </a>
        <audio v-else-if="audioTypes.includes(attachment.mime_type)"
               class="max-w-full"
               controls>
            <source :src="attachment.url" :type="attachment.mime_type" />
            {{ $t('message.element_not_supported') }}
        </audio>
        <video v-else-if="videoTypes.includes(attachment.mime_type)"
               class="max-w-full"
               controls
               height="400"
               width="400">
            <source :src="attachment.url" :type="attachment.mime_type" />
            {{ $t('message.element_not_supported') }}
        </video>
        <a v-else
           :href="attachment.url"
           class="flex space-x-2 justify-center items-center p-3 bg-gray-100 text-gray-900 w-fit rounded-md"
           download
           target="_blank">
            <ArrowDownTrayIcon aria-hidden="true" class="size-10" />
            <span class="flex-1 flex flex-col">
                <span>{{ attachment.file_name }}</span>
                <span>{{ attachment.size }}</span>
            </span>
        </a>
    </div>
</template>
