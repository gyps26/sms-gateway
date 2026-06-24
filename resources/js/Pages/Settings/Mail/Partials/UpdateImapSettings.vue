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
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from '@/Components/TextInput.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    email: settings.imap.email,
    host: settings.imap.host,
    port: settings.imap.port,
    protocol: settings.imap.protocol,
    encryption: settings.imap.encryption ? settings.imap.encryption : 'none',
    validate_cert: settings.imap.validate_cert,
    username: settings.imap.username,
    password: settings.imap.password,
    email_to_message: settings.features.email_to_message,
});

const testForm = useForm({});

const test = () => {
    testForm.post(route('settings.mail.imap.test'), {
        preserveState: true,
        preserveScroll: true,
    });
}

const updateImapSettings = () => {
    form.put(route('settings.mail.imap.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/mail-settings.html#incoming-email-configuration"
                 @submitted="updateImapSettings">
        <template #title>
            {{ $t('message.settings.imap.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.imap.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel for="to-email" :value="$t('field.email')" required />
                <TextInput
                    id="to-email"
                    v-model="form.email"
                    class="mt-1 block w-full"
                    required
                    type="text" />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-host" :value="$t('field.host')" required />
                <TextInput
                    id="imap-host"
                    v-model="form.host"
                    class="mt-1 block w-full"
                    required
                    type="text" />
                <InputError :message="form.errors.host" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-port" :value="$t('field.port')" required />
                <TextInput
                    id="imap-port"
                    v-model="form.port"
                    class="mt-1 block w-full"
                    required
                    type="number" />
                <InputError :message="form.errors.port" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-protocol" :value="$t('field.protocol')" required />
                <ComboboxInput
                    id="imap-protocol"
                    v-model="form.protocol"
                    :options="[
                        { value: 'imap', label: 'IMAP' },
                        { value: 'pop3', label: 'POP3' },
                        { value: 'nntp', label: 'NNTP' }
                    ]"
                    class="mt-1 block w-full" />
                <InputError :message="form.errors.protocol" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-encryption" :value="$t('field.encryption')" required />
                <ComboboxInput
                    id="imap-encryption"
                    v-model="form.encryption"
                    :options="[
                        { value: 'ssl', label: 'SSL' },
                        { value: 'tls', label: 'TLS' },
                        { value: 'notls', label: 'No TLS' },
                        { value: 'starttls', label: 'STARTTLS' },
                        { value: 'none', label: 'None' }
                    ]"
                    class="mt-1 block w-full" />
                <InputError :message="form.errors.encryption" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-username" :value="$t('field.username')" required />
                <TextInput
                    id="imap-username"
                    v-model="form.username"
                    class="mt-1 block w-full"
                    required
                    type="text" />
                <InputError :message="form.errors.username" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel for="imap-password" :value="$t('field.password')" required />
                <TextInput
                    id="imap-password"
                    v-model="form.password"
                    class="mt-1 block w-full"
                    required
                    type="password" />
                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="col-span-6">
                <SwitchInput v-model="form.validate_cert" :label="$t('field.validate_cert')" />
                <InputError :message="form.errors.validate_cert" class="mt-2" />
            </div>

            <div class="col-span-6">
                <SwitchInput v-model="form.email_to_message" :label="$t('field.email_to_message')" />
                <InputError :message="form.errors.email_to_message" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ $t('message.saved') }}
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="mr-3">
                {{ $t('action.save') }}
            </PrimaryButton>

            <SecondaryButton @click.prevent="test" :class="{ 'opacity-25': form.isDirty }" :disabled="form.isDirty">
                {{ $t('action.test') }}
            </SecondaryButton>
        </template>
    </FormSection>
</template>
