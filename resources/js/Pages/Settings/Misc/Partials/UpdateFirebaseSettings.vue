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
import FileInput from "@/Components/FileInput.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    _method: 'PUT',
    service_account_json: null,
});

const updateFirebaseSettings = () => {
    form.post(route('settings.misc.firebase.update'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/misc-settings.html#firebase-configuration"
                 @submitted="updateFirebaseSettings">
        <template #title>
            {{ $t('message.settings.firebase.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.firebase.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.service_account_json')" for="service-account-json" />
                <FileInput id="service-account-json" v-model="form.service_account_json"
                           accept=".json"
                           class="mt-1 block w-full" />
                <p v-if="form.service_account_json === null && $page.props.settings.firebase_project_id" class="mt-1">
                    {{ $page.props.settings.firebase_project_id }}
                </p>
                <InputError :message="form.errors.service_account_json" class="mt-2" />
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
