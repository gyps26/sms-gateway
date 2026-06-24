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
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    name: settings.mailer.from.name,
    email: settings.mailer.from.address,
});

const updateFromAddressSettings = () => {
    form.put(route('settings.mail.from.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/mail-settings.html#from-address"
                 @submitted="updateFromAddressSettings">
        <template #title>
            {{ $t('message.settings.from.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.from.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.name')" for="from-name" required />
                <TextInput
                    id="from-name"
                    v-model="form.name"
                    class="mt-1 block w-full"
                    required
                    type="text" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <div class="col-span-6">
                <InputLabel :value="$t('field.email')" for="from-address" required />
                <TextInput
                    id="from-address"
                    v-model="form.email"
                    class="mt-1 block w-full"
                    required
                    type="email" />
                <InputError :message="form.errors.email" class="mt-2" />
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
