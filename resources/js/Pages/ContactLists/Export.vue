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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import List from '@/Pages/ContactLists/List.vue';
import { ArrowDownTrayIcon, ArrowPathIcon } from '@heroicons/vue/20/solid';
import { useForm, usePoll } from '@inertiajs/vue3';
import { watch } from 'vue';

const props = defineProps({
    contactList: {
        type: Object,
        required: true,
    },
    exportStatus: {
        type: Object,
        required: true,
    },
});

const exportContactsForm = useForm({});
const clearExportForm = useForm({});

const { start, stop } = usePoll(2000, { only: ['exportStatus'] }, {
    autoStart: props.exportStatus.status !== null && props.exportStatus.status !== 'Completed',
});

const clearExport = () => {
    clearExportForm.post(route('contacts.export.clear', props.contactList), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: start,
    });
};

const exportContacts = () => {
    exportContactsForm.post(route('contacts.export.dispatch', props.contactList), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: start,
    });
};

const downloadExport = () => {
    window.location = route('contacts.export.download', props.contactList);
};

watch(() => props.exportStatus, (exportStatus) => {
    if (exportStatus.status === null || exportStatus.status === 'Completed') {
        stop();
    }
});
</script>

<template>
    <List :contact-list="contactList" title="Export Contacts">
        <div class="px-4 sm:px-6 lg:mx-auto lg:px-8">
            <div class="mt-8">
                <div v-if="exportStatus.progress !== null">
                    <div class="text-base">
                        <span v-if="exportStatus.status !== 'Completed'">
                            {{ $t('message.job.running_in_background', { job: $t('action.export') }) }}<br>
                            {{ $t('message.job.navigate_away_or_close') }}
                        </span>
                        <span v-else-if="exportStatus.status !== 'Failed'">
                            {{ $t('message.job.failed', { job: $t('action.export') }) }}
                        </span>
                        <span v-else>
                            {{ $t('message.job.processed', { job: $t('action.export') }) }}
                        </span>
                    </div>
                    <div class="mt-6 w-full bg-gray-200 rounded-sm">
                        <ProgressBar :progress="exportStatus.progress"></ProgressBar>
                    </div>
                    <div class="mt-2 text-sm text-gray-700">
                        <span v-if="exportStatus.processed > 0">
                            {{
                                $t('message.job.progress', {
                                    total: exportStatus.total,
                                    processed: exportStatus.processed,
                                    failures: exportStatus.failures,
                                })
                            }}
                        </span>
                        <span v-else>{{ $t('message.job.queued', { job: $t('action.export') }) }}</span>
                    </div>
                    <div v-if="exportStatus.status === 'Completed' || exportStatus.status === 'Failed'" class="mt-6 flex gap-4">
                        <PrimaryButton v-if="exportStatus.status === 'Completed'" type="button" @click="downloadExport">
                            <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                            {{ $t('action.download') }}
                        </PrimaryButton>
                        <SecondaryButton @click="clearExport">
                            <ArrowPathIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                            {{ $t('action.export_again') }}
                        </SecondaryButton>
                    </div>
                </div>

                <div v-else>
                    <PrimaryButton type="button" @click="exportContacts()">
                        <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $t('action.export') }}
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </List>
</template>
