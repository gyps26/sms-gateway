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
import { ClipboardDocumentCheckIcon, ClipboardDocumentIcon } from '@heroicons/vue/24/outline';
import { omit } from 'lodash';
import { ref } from 'vue';

const copied = ref(false);

const props = defineProps({
    content: {
        type: String,
        required: true,
    },
});

defineEmits(['update:modelValue']);

const copyToClipboard = () => {
    const promise = new Promise((resolve, reject) => {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(props.content)
                .then(() => resolve())
                .catch((error) => reject(error));
        } else {
            // Use the 'out of viewport hidden text area' trick
            const textArea = document.createElement('textarea');
            textArea.value = props.content;

            // Move textarea out of the viewport, so it's not visible
            textArea.style.position = 'absolute';
            textArea.style.left = '-999999px';

            document.body.prepend(textArea);
            textArea.select();

            try {
                document.execCommand('copy');
                resolve();
            } catch (error) {
                reject(error);
            } finally {
                textArea.remove();
            }
        }
    });

    promise.then(() => {
        copied.value = true;
        setTimeout(() => copied.value = false, 300);
    }).catch((error) => {
        console.error('Failed to copy to clipboard', error);
    });
};

defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <div :class="$attrs.class">
        <div class="grid grid-cols-1 grid-rows-1 mt-1">
            <div class="col-span-full row-span-full">
                <input
                    :value="content"
                    class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm pr-10"
                    disabled
                    type="text"
                    v-bind="omit($attrs, ['class'])">
            </div>
            <div class="col-span-full row-span-full justify-self-end self-center flex justify-center items-center mr-2">
                <button type="button" @click.prevent="copyToClipboard">
                    <ClipboardDocumentCheckIcon v-if="copied" class="w-5 h-5" />
                    <ClipboardDocumentIcon v-else class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>
</template>
