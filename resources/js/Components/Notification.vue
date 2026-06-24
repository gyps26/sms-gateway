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
import { useI18n } from '@/Composables/useI18n.js';
import { XMarkIcon } from '@heroicons/vue/20/solid';
import { CheckCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline';
import { usePage } from '@inertiajs/vue3';
import { reactive, watchEffect } from 'vue';

let index = 0;
const timeout = 5; // seconds

const messages = reactive(new Map());

const { t } = useI18n();

const page = usePage();

const notify = (title, message, icon, iconClass) => {
    messages.set(index, { title, message, icon, iconClass });
    setTimeout((key) => messages.delete(key), timeout * 1000, index);
    index++;
};

watchEffect(() => {
    if (page.props.flash.success) {
        notify(
            t('message.notification.success'),
            page.props.flash.success,
            CheckCircleIcon,
            'text-green-400',
        );
    } else if (page.props.flash.error) {
        notify(
            t('message.notification.error'),
            page.props.flash.error,
            XCircleIcon,
            'text-red-400',
        );
    } else if (Object.keys(page.props.errors).length > 0) {
        const errors = Object.values(page.props.errors);
        let message = errors.shift();
        let additional = errors.length;
        if (additional) {
            message = t('message.notification.additional_errors', { error: message, count: additional });
        }

        notify(
            t('message.notification.error'),
            message,
            XCircleIcon,
            'text-red-400',
        );
    }
});
</script>

<template>
    <!-- Global notification live region, render this permanently at the end of the document -->
    <div aria-live="assertive"
         class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 sm:items-start z-[100]">
        <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
            <!-- Notification panel, dynamically insert this into the live region when it needs to be displayed -->
            <template v-for="[index, notification] in messages" :key="index">
                <transition enter-active-class="transform ease-out duration-300 transition"
                            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                            leave-active-class="transition ease-in duration-100" leave-from-class="opacity-100"
                            leave-to-class="opacity-0">
                    <div
                        class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                        <div class="p-4">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <Component :is="notification.icon" :class="notification.iconClass"
                                               aria-hidden="true"
                                               class="h-6 w-6" />
                                </div>
                                <div class="ml-3 w-0 flex-1 pt-0.5">
                                    <p class="text-sm font-medium text-gray-900">{{ notification.title }}</p>
                                    <p class="mt-1 text-sm text-gray-500">{{ notification.message }}</p>
                                </div>
                                <div class="ml-4 flex-shrink-0 flex">
                                    <button
                                        class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        type="button"
                                        @click="messages.delete(index)">
                                        <span class="sr-only">{{ t('action.close') }}</span>
                                        <XMarkIcon aria-hidden="true" class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition>
            </template>
        </div>
    </div>
</template>
