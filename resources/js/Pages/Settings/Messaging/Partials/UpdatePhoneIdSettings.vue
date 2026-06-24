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
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SwitchInput from "@/Components/SwitchInput.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    'enabled': settings.phone_id.enabled,
    'contact_field_tag': settings.phone_id.contact_field_tag,
});

const updatePhoneIdSettings = () => {
    const url = page.props.global ? route('user.settings.messaging.phone-id.update') : route('settings.messaging.phone-id.update');

    form.put(url, {
        preserveState: true,
        preserveScroll: true,
    });
}
</script>

<template>
    <FormSection
        :docs="page.props.global ? '/docs/user-settings.html#identify-phone-number' : 'https://rbsoft.org/docs/sms-gateway/enhanced/messaging-settings.html#identify-phone-number'"
        @submitted="updatePhoneIdSettings">
        <template #title>
            {{ $t('message.settings.phone_id.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.phone_id.description') }}
        </template>

        <template #form>
            <div class="col-span-6">
                <SwitchInput
                    id="enabled"
                    v-model="form.enabled"
                    :label="$t('field.enabled')"
                    class="mt-1 block w-full"
                />
                <InputError :message="form.errors.enabled" class="mt-2" />
            </div>
            <div v-if="form.enabled" class="col-span-6">
                <InputLabel :value="$t('field.contact_field_tag')" for="contact-field-tag" />
                <TextInput
                    id="contact-field-tag"
                    v-model="form.contact_field_tag"
                    class="mt-1 block w-full"
                    required />
                <InputError :message="form.errors.contact_field_tag" class="mt-2" />
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
