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
import DropDownTransition from '@/Components/DropDownTransition.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import { ChevronDownIcon } from '@heroicons/vue/20/solid';
import { router } from '@inertiajs/vue3';
import { intersection } from 'lodash';
import { computed, watch } from 'vue';

const emit = defineEmits(['update:selected', 'update:selectAll']);

const props = defineProps({
    ids: {
        type: Array,
        required: true,
    },
    selected: {
        type: Array,
        default: [],
    },
    selectAll: {
        type: Boolean,
        default: false,
    },
});

const proxySelected = computed({
    get() {
        return props.selected;
    },

    set(value) {
        emit('update:selected', value);
    },
});

const proxySelectAll = computed({
    get() {
        return props.selectAll;
    },

    set(value) {
        emit('update:selectAll', value);
    },
});

watch([proxySelected, proxySelectAll], () => {
    if (proxySelected.value.length > 0 && proxySelected.value.length === props.ids.length) {
        return;
    }
    proxySelectAll.value = false;
});

router.on('finish', () => {
    if (proxySelected.value.length > 0) {
        proxySelected.value = intersection(proxySelected.value, props.ids.value);
    }
});
</script>

<template>
    <Menu class="relative text-left">
        <MenuButton class="absolute top-0 w-full h-full flex items-center justify-center">
            <ChevronDownIcon aria-hidden="true" class="h-6 w-6" />
        </MenuButton>

        <DropDownTransition>
            <MenuItems
                class="font-normal origin-top-left absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                <div class="py-1">
                    <MenuItem v-slot="{ active }">
                        <button
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm w-full text-left']"
                            @click="proxySelectAll = false; proxySelected = ids;">
                            {{ $t('action.select_visible') }}
                        </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <button
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm w-full text-left']"
                            @click="proxySelectAll = true; proxySelected = ids;">
                            {{ $t('action.select_all') }}
                        </button>
                    </MenuItem>
                    <MenuItem v-if="proxySelected.length > 0" v-slot="{ active }">
                        <button
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'block px-4 py-2 text-sm w-full text-left']"
                            @click="proxySelectAll = false; proxySelected = [];">
                            {{ $t('action.deselect_all') }}
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </DropDownTransition>
    </Menu>
</template>
