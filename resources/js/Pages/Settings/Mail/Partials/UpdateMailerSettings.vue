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
import DialogModal from "@/Components/DialogModal.vue";
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SwitchInput from "@/Components/SwitchInput.vue";
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from "vue";

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    mailer: settings.mailer.default,
    sendmail: {
        path: settings.mailers.sendmail.path,
    },
    smtp: {
        host: settings.mailers.smtp.host,
        port: settings.mailers.smtp.port,
        username: settings.mailers.smtp.username,
        password: settings.mailers.smtp.password,
        encryption: settings.mailers.smtp.encryption,
    },
    mailgun: {
        domain: settings.mailers.mailgun.domain,
        secret: settings.mailers.mailgun.secret,
        endpoint: settings.mailers.mailgun.endpoint,
    },
    ses: {
        key: settings.mailers.ses.key,
        secret: settings.mailers.ses.secret,
        region: settings.mailers.ses.region,
    },
    postmark: {
        token: settings.mailers.postmark.token,
    },
    resend: {
        key: settings.mailers.resend.key,
    },
    message_to_email: settings.features.message_to_email,
});

const testing = ref(false);

const testForm = useForm({
    email: null
});

const test = () => {
    testForm.post(route('settings.mail.mailer.test'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => testing.value = false
    });
}

const updateMailerSettings = () => {
    form.put(route('settings.mail.mailer.update'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div>
        <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/mail-settings.html#outgoing-email-configuration"
                     @submitted="updateMailerSettings">
            <template #title>
                {{ $t('message.settings.mailer.title') }}
            </template>

            <template #description>
                {{ $t('message.settings.mailer.description') }}
            </template>

            <template #form>
                <div class="col-span-6">
                    <InputLabel :value="$t('field.mailer')" for="mailer" />
                    <ComboboxInput
                        id="mailer"
                        v-model="form.mailer"
                        :options="[
                        { value: 'log', label: 'Log' },
                        { value: 'sendmail', label: 'Sendmail' },
                        { value: 'smtp', label: 'SMTP' },
                        { value: 'mailgun', label: 'Mailgun' },
                        { value: 'ses', label: 'Amazon SES' },
                        { value: 'postmark', label: 'Postmark' },
                        { value: 'resend', label: 'Resend' },
                    ]"
                        class="mt-1 block w-full" />

                    <InputError :message="form.errors.mailer" class="mt-2" />
                </div>

                <template v-if="form.mailer === 'sendmail'">
                    <div class="col-span-6">
                        <InputLabel for="sendmail-path" required value="Sendmail Path" />
                        <TextInput
                            id="sendmail-path"
                            v-model="form.sendmail.path"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['sendmail.path']" class="mt-2" />
                    </div>
                </template>

                <template v-if="form.mailer === 'smtp'">
                    <div class="col-span-6">
                        <InputLabel for="smtp-host" required value="Host" />
                        <TextInput
                            id="smtp-host"
                            v-model="form.smtp.host"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['smtp.host']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="smtp-encryption" required value="Encryption" />
                        <TextInput
                            id="smtp-encryption"
                            v-model="form.smtp.encryption"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['smtp.encryption']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="smtp-port" required value="Port" />
                        <TextInput
                            id="smtp-port"
                            v-model="form.smtp.port"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['smtp.port']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="smtp-username" required value="Username" />
                        <TextInput
                            id="smtp-username"
                            v-model="form.smtp.username"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['smtp.username']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="smtp-password" required value="Password" />
                        <TextInput
                            id="smtp-password"
                            v-model="form.smtp.password"
                            class="mt-1 block w-full"
                            required
                            type="password" />
                        <InputError :message="form.errors['smtp.password']" class="mt-2" />
                    </div>
                </template>

                <template v-if="form.mailer === 'mailgun'">
                    <div class="col-span-6">
                        <InputLabel for="mailgun-domain" required value="Domain" />
                        <TextInput
                            id="mailgun-domain"
                            v-model="form.mailgun.domain"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['mailgun.domain']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="mailgun-secret" required value="Secret" />
                        <TextInput
                            id="mailgun-secret"
                            v-model="form.mailgun.secret"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['mailgun.secret']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="mailgun-endpoint" required value="Endpoint" />
                        <TextInput
                            id="mailgun-endpoint"
                            v-model="form.mailgun.endpoint"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['mailgun.endpoint']" class="mt-2" />
                    </div>
                </template>

                <template v-if="form.mailer === 'ses'">
                    <div class="col-span-6">
                        <InputLabel for="ses-key" required value="Key" />
                        <TextInput
                            id="ses-key"
                            v-model="form.ses.key"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['ses.key']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="ses-secret" required value="Secret" />
                        <TextInput
                            id="ses-secret"
                            v-model="form.ses.secret"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['ses.secret']" class="mt-2" />
                    </div>

                    <div class="col-span-6">
                        <InputLabel for="ses-region" required value="Region" />
                        <TextInput
                            id="ses-region"
                            v-model="form.ses.region"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['ses.region']" class="mt-2" />
                    </div>
                </template>

                <template v-if="form.mailer === 'postmark'">
                    <div class="col-span-6">
                        <InputLabel for="postmark-token" required value="Token" />
                        <TextInput
                            id="postmark-token"
                            v-model="form.postmark.token"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['postmark.token']" class="mt-2" />
                    </div>
                </template>

                <template v-if="form.mailer === 'resend'">
                    <div class="col-span-6">
                        <InputLabel for="resend-key" required value="Key" />
                        <TextInput
                            id="resend-key"
                            v-model="form.resend.key"
                            class="mt-1 block w-full"
                            required
                            type="text" />
                        <InputError :message="form.errors['resend.key']" class="mt-2" />
                    </div>
                </template>

                <div class="col-span-6">
                    <SwitchInput v-model="form.message_to_email" :label="$t('field.message_to_email')" />
                    <InputError :message="form.errors.message_to_email" class="mt-2" />
                </div>
            </template>

            <template #actions>
                <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                    {{ $t('message.saved') }}
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="mr-3">
                    {{ $t('action.save') }}
                </PrimaryButton>

                <SecondaryButton :class="{ 'opacity-25': form.isDirty }" :disabled="form.isDirty"
                                 @click.prevent="testing = true">
                    {{ $t('action.test') }}
                </SecondaryButton>
            </template>
        </FormSection>

        <!-- Test Mail Modal -->
        <DialogModal :show="testing" @close="testing = false">
            <template #title>
                {{ $t('message.settings.mailer.test') }}
            </template>

            <template #content>
                <form id="test" @submit.prevent="test">
                    <InputLabel :value="$t('field.email')" for="email" />
                    <TextInput id="email" v-model="testForm.email" autofocus class="mt-1 block w-full"
                               type="email" />
                    <InputError :message="testForm.errors.email" class="mt-2" />
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="testing = false">
                    {{ $t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': testForm.processing }"
                    :disabled="testForm.processing"
                    class="ml-3"
                    form="test"
                >
                    {{ $t('action.send') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </div>
</template>
