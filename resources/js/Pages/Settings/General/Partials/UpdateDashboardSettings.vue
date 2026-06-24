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
import SwitchInput from '@/Components/SwitchInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const settings = page.props.settings;

const form = useForm({
    campaigns: settings.dashboard.stats.campaigns,
    calls: settings.dashboard.stats.calls,
    messages: settings.dashboard.stats.messages,
    ussd_pulls: settings.dashboard.stats.ussd_pulls,
    realtime: settings.dashboard.realtime,
});

const updateDashboardSettings = () => {
    const url = page.props.global ? route('user.settings.general.dashboard.update') : route('settings.general.dashboard.update');

    form.put(url, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <FormSection :docs="page.props.global ? '/docs/user-settings.html#dashboard' : 'https://rbsoft.org/docs/sms-gateway/enhanced/general-settings.html#dashboard'"
                 @submitted="updateDashboardSettings">
        <template #title>
            {{ $t('message.settings.dashboard.title') }}
        </template>
        <template #description>
            {{ $t('message.settings.dashboard.description') }}
        </template>
        <template #form>
            <fieldset class="col-span-6 p-4 pb-6 border border-solid border-gray-300 rounded-md">
                <legend>{{ $t('field.stats') }}</legend>
                <div class="space-y-4">
                    <div>
                        <SwitchInput v-model="form.campaigns" :label="$t('message.dashboard.section.campaigns')" />
                        <InputError :message="form.errors.campaigns" class="mt-2" />
                    </div>
                    <div>
                        <SwitchInput v-model="form.calls" :label="$t('message.dashboard.section.calls')" />
                        <InputError :message="form.errors.calls" class="mt-2" />
                    </div>
                    <div>
                        <SwitchInput v-model="form.messages" :label="$t('message.dashboard.section.messages')" />
                        <InputError :message="form.errors.messages" class="mt-2" />
                    </div>
                    <div>
                        <SwitchInput v-model="form.ussd_pulls" :label="$t('message.dashboard.section.ussd_pulls')" />
                        <InputError :message="form.errors.ussd_pulls" class="mt-2" />
                    </div>
                </div>
            </fieldset>
            <div class="col-span-6">
                <SwitchInput v-model="form.realtime" :label="$t('field.realtime')" />
                <InputError :message="form.errors.realtime" class="mt-2" />
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
