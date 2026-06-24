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
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import CopyInput from "@/Components/CopyInput.vue";

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    message_to_email: settings.message_to_email,
    email_to_message: settings.email_to_message,
});

const updateFeaturesSettings = () => {
    form.put(route('user.settings.mail.features.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="/docs/user-settings.html#features" @submitted="updateFeaturesSettings">
        <template #title>
            {{ $t('message.settings.features.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.features.description') }}
        </template>

        <template #form>
            <div v-if="page.props.global.email_to_message" class="col-span-6">
                <SwitchInput v-model="form.email_to_message" :label="$t('field.email_to_message')" />
                <InputError :message="form.errors.email_to_message" class="mt-2" />
                <CopyInput id="email"
                           v-if="page.props.settings.email_to_message"
                           :content="page.props.global.email"
                           class="mt-4 block w-full" />
            </div>

            <div v-if="page.props.global.message_to_email" class="col-span-6">
                <SwitchInput v-model="form.message_to_email" :label="$t('field.message_to_email')" />
                <InputError :message="form.errors.message_to_email" class="mt-2" />
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
