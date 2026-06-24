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
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ProgressBar from '@/Components/ProgressBar.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { ArrowDownTrayIcon, ArrowPathIcon, DocumentArrowUpIcon } from '@heroicons/vue/20/solid';
import { useForm, usePage, usePoll } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    params: {
        type: Object,
        required: true,
    },
});

const page = usePage();

const showExportDialog = ref(false);

const { start, stop } = usePoll(2000, { preserveUrl: true, only: ['exportStatus'] }, {
    autoStart: page.props.exportStatus.status !== null && page.props.exportStatus.status !== 'Completed',
});

const exportMessagesForm = useForm({
    ...props.params,
});

const exportMessages = () => {
    Object.assign(exportMessagesForm, props.params);
    exportMessagesForm.post(route('messages.export.dispatch'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            start();
            showExportDialog.value = true;
        },
    });
};

const downloadExport = () => {
    window.location = route('messages.export.download');
};

watch(() => page.props.exportStatus, (exportStatus) => {
    if (exportStatus.status === null || exportStatus.status === 'Completed') {
        stop();
    }
});
</script>

<template>
    <PrimaryButton type="button"
                   @click="page.props.exportStatus.status !== null ? showExportDialog = true : exportMessages()">
        <DocumentArrowUpIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
        {{ $t('action.export') }}
    </PrimaryButton>

    <DialogModal :show="showExportDialog" @close="showExportDialog = false">
        <template #title>
            {{ $t('message.messages.export') }}
        </template>

        <template #content>
            <div class="text-base">
                <span v-if="page.props.exportStatus.status !== 'Completed'">
                    {{ $t('message.job.running_in_background', { job: $t('action.export') }) }}<br>
                    {{ $t('message.job.navigate_away_or_close') }}
                </span>
                <span v-else-if="page.props.exportStatus.status === 'Failed'">
                    {{ $t('message.job.failed', { job: $t('action.export') }) }}
                </span>
                <span v-else>
                    {{ $t('message.job.processed', { job: $t('action.export') }) }}
                </span>
            </div>
            <div class="mt-6 w-full bg-gray-200 rounded-sm">
                <ProgressBar :progress="page.props.exportStatus.progress"></ProgressBar>
            </div>
            <div class="mt-2 text-sm text-gray-700">
                <span v-if="page.props.exportStatus.processed > 0">
                    {{
                        $t('message.job.progress', {
                            total: page.props.exportStatus.total,
                            processed: page.props.exportStatus.processed,
                            failures: page.props.exportStatus.failures,
                        })
                    }}
                </span>
                <span v-else>{{ $t('message.job.queued', { job: $t('action.export') }) }}</span>
            </div>
        </template>

        <template v-if="page.props.exportStatus.status === 'Completed' || page.props.exportStatus.status === 'Failed'" #footer>
            <PrimaryButton v-if="page.props.exportStatus.status === 'Completed'" type="button" @click="downloadExport">
                <ArrowDownTrayIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                {{ $t('action.download') }}
            </PrimaryButton>
            <SecondaryButton class="ml-3" @click="exportMessages">
                <ArrowPathIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                {{ $t('action.export_again') }}
            </SecondaryButton>
        </template>
        <template v-else #footer>
            <SecondaryButton @click="showExportDialog = false">
                {{ $t('action.close') }}
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
