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
import ToggleableInput from '@/Components/ToggleableInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    lifespan: settings.qr_code.lifespan,
});

const updateQrCodeSettings = () => {
    const url = page.props.global ? route('user.settings.misc.qr-code.update') : route('settings.misc.qr-code.update');

    form.put(url, {
        preserveState: true,
        preserveScroll: true,
    });
};


</script>

<template>
    <FormSection :docs="page.props.global ? '/docs/user-settings.html#qr-code' : 'https://rbsoft.org/docs/sms-gateway/enhanced/misc-settings.html#qr-code'"
                 @submitted="updateQrCodeSettings">
        <template #title>
            {{ $t('message.settings.qr_code.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.qr_code.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <InputLabel :value="$t('field.lifespan')" for="lifespan" />
                <ToggleableInput id="lifespan"
                                 v-model="form.lifespan"
                                 class="mt-1 block w-full"
                                 min="60"
                                 type="number" />
                <InputError :message="form.errors.lifespan" class="mt-2" />
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
