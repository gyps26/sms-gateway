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
import { useForm, usePage } from "@inertiajs/vue3";
import FormSection from "@/Components/FormSection.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SwitchInput from "@/Components/SwitchInput.vue";
import InputError from "@/Components/InputError.vue";
import ActionMessage from "@/Components/ActionMessage.vue";

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    wait_for_confirmation: settings.sim.wait_for_confirmation
});

const updateSimSettings = () => {
    const url = page.props.global ? route('user.settings.messaging.sim.update') : route('settings.messaging.sim.update');

    form.put(url, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <FormSection :docs="page.props.global ? '/docs/user-settings.html#sim' : 'https://rbsoft.org/docs/sms-gateway/enhanced/messaging-settings.html#sim'"
                 @submitted="updateSimSettings">
        <template #title>
            {{ $t('message.settings.sim.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.sim.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <SwitchInput
                    id="wait-for-confirmation"
                    v-model="form.wait_for_confirmation"
                    :label="$t('field.wait_for_confirmation')"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.wait_for_confirmation" class="mt-2" />
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
