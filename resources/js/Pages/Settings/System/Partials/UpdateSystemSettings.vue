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
import TextInput from "@/Components/TextInput.vue";
import { useFormatter } from "@/Composables/useFormatter.js";
import { ExclamationTriangleIcon } from "@heroicons/vue/20/solid";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    _method: 'PUT',
    update: null
});

const updateSystem = () => {
    form.post(route('settings.system.update'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/system.html#system-information"
                 @submitted="updateSystem">
        <template #title>
            {{ $t('message.settings.system.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.system.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.version')" for="version" />
                <TextInput
                    id="version"
                    :value="$page.props.version"
                    class="mt-1 block w-full"
                    disabled />
            </div>
            <div class="col-span-6">
                <InputLabel :value="$t('field.update')" for="update" />
                <FileInput
                    id="update"
                    v-model="form.update"
                    accept=".zip"
                    class="mt-1 block w-full"
                    required />
                <InputError :message="form.errors.update" class="mt-2" />
                <div class="border-l-4 border-yellow-400 bg-yellow-50 p-4 mt-2">
                    <div class="flex">
                        <div class="shrink-0">
                            <ExclamationTriangleIcon aria-hidden="true" class="size-5 text-yellow-400" />
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700"
                               v-html="$t('message.settings.system.update_warning', { size: useFormatter().formatBytes($page.props.maxUploadSize) })">
                            </p>
                        </div>
                    </div>
                </div>
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
