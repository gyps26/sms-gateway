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
import { ArrowUpTrayIcon } from "@heroicons/vue/24/outline";
import { omit } from 'lodash';
import { computed, ref } from "vue";

defineOptions({
    inheritAttrs: false
})

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: [File, FileList],
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    }
});

const fileInput = ref(null);

const proxyModelValue = computed({
    get() {
        return props.modelValue;
    },

    set(value) {
        emit('update:modelValue', value);
    },
});

const text = computed(() => {
    if (proxyModelValue.value) {
        return props.multiple
            ? Array.from(proxyModelValue.value).map(f => f.name).join(', ')
            : proxyModelValue.value.name;
    }

    return null;
});
</script>

<template>
    <div :class="$attrs.class">
        <input
            ref="fileInput"
            :multiple="multiple"
            class="hidden"
            type="file"
            v-bind="omit($attrs, ['class'])"
            @change="proxyModelValue = multiple ? $event.target.files : $event.target.files[0]"
        >

        <div class="flex items-center justify-center">
            <div class="flex-1">
                <input
                    :required="required"
                    :value="text"
                    class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-s-md shadow-sm"
                    type="text"
                    tabindex="-1"
                    @focus.prevent="false"
                    @paste.prevent="false"
                    @keydown.prevent="false">
            </div>
            <div>
                <button
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-e-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                    type="button"
                    @click.prevent="fileInput.click()">
                    <ArrowUpTrayIcon class="size-6" />
                </button>
            </div>
        </div>
    </div>
</template>
