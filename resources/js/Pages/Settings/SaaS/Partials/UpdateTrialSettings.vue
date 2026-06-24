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
import ComboboxInput from '@/Components/ComboboxInput.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import ToggleableInput from '@/Components/ToggleableInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    footer: settings.trial.footer,
    plan: settings.trial.plan_id,
    duration: settings.trial.duration,
});

const updateTrialSettings = () => {
    form.put(route('settings.saas.trial.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/saas-settings.html#trial"
                 @submitted="updateTrialSettings">
        <template #title>
            {{ $t('message.settings.trial.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.trial.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.plan')" for="plan" />
                <ComboboxInput
                    id="plan"
                    v-model="form.plan"
                    :options="page.props.plans"
                    class="mt-1 block w-full"
                    text-attribute="label"
                    value-attribute="id" />
                <InputError :message="form.errors.plan" class="mt-2" />
            </div>
            <template v-if="form.plan">
                <div class="col-span-6">
                    <InputLabel :value="$t('field.duration')" for="duration" />
                    <ToggleableInput
                        id="duration"
                        v-model="form.duration"
                        class="mt-1 block w-full"
                        min="1"
                        type="number"
                    />
                    <InputError :message="form.errors.duration" class="mt-2" />
                </div>
                <div class="col-span-6">
                    <InputLabel :value="$t('field.footer')" for="footer" />
                    <TextAreaInput
                        id="footer"
                        v-model="form.footer"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.footer" class="mt-2" />
                </div>
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
