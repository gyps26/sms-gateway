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

defineEmits(['select']);

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <Menu as="div" class="relative inline-block text-left">
        <div>
            <MenuButton
                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <slot />
                <ChevronDownIcon aria-hidden="true" class="-mr-1 ml-2 h-5 w-5" />
            </MenuButton>
        </div>

        <DropDownTransition>
            <MenuItems
                class="origin-top-right absolute left-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 focus:outline-none z-50">
                <div v-for="group in items" class="py-1">
                    <MenuItem v-for="item in group" v-slot="{ active }">
                        <button
                            :class="[active ? 'bg-gray-100 text-gray-900' : 'text-gray-700', 'group flex items-center px-4 py-2 text-base w-full text-left']"
                            @click="$emit('select', item)">
                            <Component :is="item.icon" v-if="item.icon"
                                       aria-hidden="true"
                                       class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" />
                            {{ item.name }}
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </DropDownTransition>
    </Menu>
</template>
