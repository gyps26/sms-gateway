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
    received: settings.credits.received.amount,
    sms: settings.credits.sms.amount,
    mms: settings.credits.mms.amount,
    ussd_pull: settings.credits.ussd_pull.amount,
    call: settings.credits.call.amount,
    webhook_call: settings.credits.webhook_call.amount,
    message_to_email: settings.credits.message_to_email.amount,
    email_to_message: settings.credits.email_to_message.amount,
    per_part: settings.credits.sms.per_part,
});

const updateCreditsSettings = () => {
    form.put(route('settings.saas.credits.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/saas-settings.html#credits"
                 @submitted="updateCreditsSettings">
        <template #title>
            {{ $t('message.settings.credits.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.credits.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.received')" for="received" required />
                <TextInput
                    id="received"
                    v-model="form.received"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.received" class="mt-2" />
            </div>
            <div class="col-span-6 flex items-center space-x-4">
                <div class="flex-1">
                    <InputLabel :value="$t('field.sms')" for="sms" required />
                    <TextInput
                        id="sms"
                        v-model="form.sms"
                        class="mt-1 block w-full"
                        max="10"
                        min="0"
                        required
                        type="number" />
                    <InputError :message="form.errors.sms" class="mt-2" />
                </div>
                <div>
                    <SwitchInput v-model="form.per_part" :label="$t('field.per_part')" class="mt-5" />
                    <InputError :message="form.errors.per_part" class="mt-2" />
                </div>
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.mms')" for="mms" required />
                <TextInput
                    id="mms"
                    v-model="form.mms"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.mms" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.ussd_pull')" for="ussd_pull" required />
                <TextInput
                    id="ussd_pull"
                    v-model="form.ussd_pull"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.ussd_pull" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.call')" for="call" required />
                <TextInput
                    id="call"
                    v-model="form.call"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.call" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.webhook_call')" for="webhook-call" required />
                <TextInput
                    id="webhook-call"
                    v-model="form.webhook_call"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.webhook_call" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.message_to_email')" for="message-to-email" required />
                <TextInput
                    id="message-to-email"
                    v-model="form.message_to_email"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.message_to_email" class="mt-2" />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.email_to_message')" for="email-to-message" required />
                <TextInput
                    id="email-to-message"
                    v-model="form.email_to_message"
                    class="mt-1 block w-full"
                    max="10"
                    min="0"
                    required
                    type="number" />
                <InputError :message="form.errors.email_to_message" class="mt-2" />
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
