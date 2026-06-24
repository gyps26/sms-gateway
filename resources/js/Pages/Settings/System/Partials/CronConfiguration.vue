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
import ActionMessage from "@/Components/ActionMessage.vue";
import CopyInput from '@/Components/CopyInput.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from "@/Components/InputError.vue";
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SwitchInput from "@/Components/SwitchInput.vue";
import { useDateTime } from '@/Composables/useDateTime.js';
import { ExclamationCircleIcon } from '@heroicons/vue/16/solid';
import { useForm, usePage } from "@inertiajs/vue3";

const page = usePage();

const form = useForm({
    queue_up: page.props.queueUp,
});

const cronError = page.props.cron.executed_at === null || new Date() - new Date(page.props.cron.executed_at) > 60 * 1000;

const updateCronSettings = () => {
    form.put(route('settings.system.cron.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/system.html#cron-job-configuration"
                 @submitted="updateCronSettings">
        <template #title>
            {{ $t('message.settings.cron.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.cron.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.command')" for="command" />
                <CopyInput id="command"
                           :content="page.props.cron.command"
                           class="mt-1 block w-full" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.last_executed_at')" for="executed-at" />
                <div class="mt-2 grid grid-cols-1">
                    <input id="executed-at"
                           :aria-describedby="cronError ? 'cron-error' : null" :aria-invalid="cronError"
                           :class="['col-start-1 row-start-1 block w-full rounded-md shadow-sm pr-10', cronError ? 'border-red-300 ring-red-200 ring' : 'border-gray-300']"
                           :value="useDateTime().getISOString(page.props.cron.executed_at)"
                           disabled
                           type="datetime-local">
                    <ExclamationCircleIcon v-if="cronError"
                                           aria-hidden="true"
                                           class="pointer-events-none col-start-1 row-start-1 mr-3 size-5 self-center justify-self-end text-red-500 sm:size-4" />
                </div>
                <p v-if="cronError" id="cron-error" class="mt-2 text-sm text-red-600">The cron job has not run recently.
                    Please ensure that your cron job is set up to run every minute using the above command.</p>
            </div>
            <div class="col-span-6">
                <SwitchInput v-model="form.queue_up" :label="$t('field.queue_up')" />
                <InputError :message="form.errors.queue_up" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ $t('message.saved') }}
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('action.save') }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>
