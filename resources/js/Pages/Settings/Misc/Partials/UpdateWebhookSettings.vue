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
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    verify_ssl: settings.webhook_server.verify_ssl,
    tries: settings.webhook_server.tries,
    timeout: settings.webhook_server.timeout_in_seconds,
});

const updateWebhookSettings = () => {
    form.put(route('settings.misc.webhook.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/misc-settings.html#webhook-configuration"
                 @submitted="updateWebhookSettings">
        <template #title>
            {{ $t('message.settings.webhook.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.webhook.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.tries')" for="tries" required />
                <TextInput
                    id="tries"
                    v-model="form.tries"
                    autofocus
                    class="mt-1 block w-full"
                    required
                    type="number" />
                <InputError :message="form.errors.tries" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.timeout')" for="timeout" required />
                <TextInput
                    id="timeout"
                    v-model="form.timeout"
                    class="mt-1 block w-full"
                    required
                    type="number" />
                <InputError :message="form.errors.timeout" class="mt-2" />
            </div>
            <div class="col-span-6">
                <SwitchInput id="verify-ssl"
                             v-model="form.verify_ssl" :label="$t('field.verify_ssl_webhook')"
                             class="mt-1 block w-full" />
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
