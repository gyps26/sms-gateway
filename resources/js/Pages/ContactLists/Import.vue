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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SpreadsheetUploadInfo from '@/Components/SpreadsheetUploadInfo.vue';
import List from '@/Pages/ContactLists/List.vue';
import { ArrowDownTrayIcon, ArrowPathIcon, ArrowUpTrayIcon } from '@heroicons/vue/20/solid';
import { useForm, usePoll } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    maxUploadSize: {
        type: Number,
        required: true,
    },
    contactList: {
        type: Object,
        required: true,
    },
    importStatus: {
        type: Object,
        required: false,
    },
});

const excelInput = ref(null);
const cancelingImport = ref(false);

const importContactsForm = useForm({
    file: null,
});

const cancelImportForm = useForm({});
const clearImportForm = useForm({});

const { start, stop } = usePoll(2000, { only: ['importStatus'] }, {
    autoStart: props.importStatus.progress !== null && props.importStatus.progress < 100,
});

const cancelImport = () => {
    cancelImportForm.post(route('contacts.import.cancel', props.contactList), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => cancelingImport.value = false,
    });
};

const clearImport = () => {
    clearImportForm.post(route('contacts.import.clear', props.contactList), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: start,
    });
};

const downloadImportLog = () => {
    window.location = route('contacts.import.log', props.contactList);
};

const importContacts = () => {
    const file = excelInput.value.files[0];

    if (! file) return;

    importContactsForm.file = file;

    importContactsForm.post(route('contacts.import.dispatch', props.contactList), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            importContactsForm.reset();
            start();
        },
    });
};

watch(() => props.importStatus, (importStatus) => {
    if (importStatus.progress === null || importStatus.progress === 100) {
        stop();
    }
});
</script>

<template>
    <List :contact-list="contactList" title="Import Contacts">
        <div class="px-4 sm:px-6 lg:mx-auto lg:px-8">
            <SpreadsheetUploadInfo :max-upload-size="maxUploadSize" :sample="route('contacts.import.sample', contactList.id)"
                                   class="p-4 mt-8">
                {{
                    $t('message.contacts.import_info', {
                        columns: `mobile_number, subscribed, ${ contactList.fields.map((field) => field.tag).join(', ') }`,
                    })
                }}
            </SpreadsheetUploadInfo>

            <div v-if="importStatus.progress !== null" class="mt-8">
                <div class="text-base">
                    <span v-if="importStatus.progress !== 100">
                        {{ $t('message.job.running_in_background', { job: $t('action.import') }) }}<br>
                        {{ $t('message.job.navigate_away_or_close') }}
                    </span>
                    <span v-else>
                        {{ $t('message.job.processed', { job: $t('action.import') }) }}<br>
                        {{ $t('message.contacts.import_finished') }}
                    </span>
                </div>
                <div class="mt-6 w-full bg-gray-200 rounded-sm">
                    <ProgressBar :progress="importStatus.progress"></ProgressBar>
                </div>
                <div class="mt-2 text-sm text-gray-700">
                    <span v-if="importStatus.cancelled">
                        {{ $t('message.job.being_cancelled', { job: $t('action.import') }) }}
                    </span>
                    <span v-else-if="importStatus.processed > 0">
                        {{
                            $t('message.job.progress', {
                                total: importStatus.total,
                                processed: importStatus.processed,
                                failures: importStatus.failures,
                            })
                        }}
                    </span>
                    <span v-else>{{ $t('message.job.queued', { job: $t('action.import') }) }}</span>
                </div>
                <div v-if="importStatus.progress === 100" class="mt-6 flex gap-4">
                    <PrimaryButton @click="downloadImportLog">
                        <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $t('action.download_log') }}
                    </PrimaryButton>
                    <SecondaryButton @click="clearImport">
                        <ArrowPathIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $t('action.import_another') }}
                    </SecondaryButton>
                </div>
                <div v-else class="mt-4">
                    <PrimaryButton :disabled="importStatus.cancelled" @click="cancelingImport = true">
                        {{ $t('action.cancel') }}
                    </PrimaryButton>
                </div>
            </div>

            <div v-else class="mt-8 flex items-center">
                <input
                    ref="excelInput"
                    class="hidden"
                    type="file"
                    @change="importContacts"
                >
                <PrimaryButton type="button" @click="excelInput.click()">
                    <ArrowUpTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ $t('action.import') }}
                </PrimaryButton>
            </div>
        </div>

        <!-- Cancel Import Confirmation Modal -->
        <ConfirmationModal :show="cancelingImport" @close="cancelingImport = false">
            <template #title>
                {{ $t('action.cancel') }} {{ $t('action.import') }}
            </template>

            <template #content>
                {{ $t('message.import.cancel_confirmation') }}
            </template>

            <template #footer>
                <SecondaryButton @click="cancelingImport = false">
                    {{ $t('action.no') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': cancelImportForm.processing }"
                    :disabled="cancelImportForm.processing"
                    class="ml-3"
                    @click="cancelImport"
                >
                    {{ $t('action.yes') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </List>
</template>
