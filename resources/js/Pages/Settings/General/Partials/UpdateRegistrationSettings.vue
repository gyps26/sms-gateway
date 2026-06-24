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
import { ref } from 'vue';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    enabled: settings.registration,
    recaptcha: {
        site_key: settings.recaptcha.site_key,
        secret_key: settings.recaptcha.secret_key,
    },
});

const recaptcha = ref(settings.recaptcha.site_key !== null);

const updateRegistrationSettings = () => {
    form.transform((data) => ({
        ...data,
        recaptcha: recaptcha.value ? {
            site_key: form.recaptcha.site_key,
            secret_key: form.recaptcha.secret_key,
        } : null,
    })).put(route('settings.general.registration.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/general-settings.html#registration"
                 @submitted="updateRegistrationSettings">
        <template #title>
            {{ $t('message.settings.registration.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.registration.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <SwitchInput v-model="form.enabled" :label="$t('field.enabled')" />
                <InputError :message="form.errors.enabled" class="mt-2" />
            </div>

            <template v-if="form.enabled">
                <div class="col-span-6">
                    <SwitchInput v-model="recaptcha" :label="$t('field.show_captcha')" />
                </div>

                <template v-if="recaptcha">
                    <div class="col-span-6">
                        <InputLabel for="recaptcha-site-key" value="reCAPTCHA v2 Site Key" required />
                        <TextInput
                            id="recaptcha-site-key"
                            v-model="form.recaptcha.site_key"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['recaptcha.site_key']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="recaptcha-secret-key" value="reCAPTCHA v2 Secret Key" required />
                        <TextInput
                            id="recaptcha-secret-key"
                            v-model="form.recaptcha.secret_key"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['recaptcha.secret_key']" class="mt-2" />
                    </div>
                </template>
            </template>
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
