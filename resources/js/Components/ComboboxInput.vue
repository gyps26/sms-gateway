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
import { debounce, isObject } from 'lodash';
import { NSelect } from 'naive-ui';
import { computed, nextTick, onMounted, ref, watch } from 'vue';

const emit = defineEmits(['change']);

const props = defineProps({
    clearable: {
        type: Boolean,
        default: true,
    },
    hideSearchBox: {
        type: Boolean,
        default: false,
    },
    options: {
        type: Object,
        default: () => [],
    },
    textAttribute: {
        type: String,
        default: 'label',
    },
    valueAttribute: {
        type: String,
        default: 'value',
    },
    fetchOptions: {
        type: Function,
        default: null,
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    autofocus: {
        type: Boolean,
        default: false,
    }
});

const selectInput = ref(null);

const loadingRef = ref(false);
const optionsRef = ref(props.options);

const remote = computed(() => props.fetchOptions != null);

const model = defineModel();

const proxyOptions = computed({
    get() {
        if (Array.isArray(optionsRef.value)) {
            return optionsRef.value.map(option => {
                if (typeof option === 'object') {
                    return option;
                }

                return {
                    label: option,
                    value: option,
                };
            });
        } else if (isObject(optionsRef.value)) {
            return Object.keys(optionsRef.value).map(key => ({ label: optionsRef.value[key], value: key }));
        } else {
            return [];
        }
    },

    set(value) {
        optionsRef.value = value;
    },
});

const search = debounce((query) => {
    if (query.length >= 1) {
        loadingRef.value = true;
        props.fetchOptions(query).then((options) => {
            optionsRef.value = options;
        }).finally(() => {
            loadingRef.value = false;
        });
    }
}, 300);

watch(() => props.options, (options) => {
    optionsRef.value = options;
});

const themeOverrides = {
    peers: {
        InternalSelection: {
            textColor: '#000000',
            border: '1px solid #D1D5DB',
            borderRadius: '0.375rem',
            borderHover: '1px solid #D1D5DB',
            borderFocus: '1px solid #A5B4FC',
            borderActive: '1px solid #A5B4FC',
            boxShadowHover: '0 1px 2px 0 rgba(0, 0, 0, 0.05)',
            boxShadowFocus: '0 0 0 3px rgba(199, 210, 254, 0.5)',
            boxShadowActive: '0 0 0 3px rgba(199, 210, 254, 0.5)',
            caretColor: '#000000',
        },
        InternalSelectMenu: {
            optionTextColorActive: '#4F46E5',
            optionCheckColor: '#4F46E5',
            loadingColor: '#000000',
        },
    },
};

onMounted(() => {
    if (props.autofocus && selectInput.value) {
        nextTick(() => selectInput.value.focus());
    }
})
</script>

<template>
    <n-select ref="selectInput" v-model:value="model" :clearable="clearable"
              :filterable="!hideSearchBox" :label-field="textAttribute" :loading="loadingRef" :multiple="multiple"
              :options="proxyOptions" :remote="remote" :theme-overrides="themeOverrides"
              :value-field="valueAttribute" max-tag-count="responsive" placeholder=""
              size="large" @[remote&&`search`]="search" @update:value="(value) => emit('change', value)">
        <template v-if="remote" #header>
            {{ $t('message.combobox.search') }}
        </template>
        <template v-if="multiple" #action>
            <span class="cursor-pointer" @click="model = proxyOptions.map(o => o[valueAttribute])">
                {{ $t('action.select_all') }}
            </span>
        </template>
    </n-select>
</template>
