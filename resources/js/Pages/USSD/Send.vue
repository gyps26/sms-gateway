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
import ComboboxInput from '@/Components/ComboboxInput.vue';
import FormGroup from '@/Components/FormGroup.vue';
import FormLayout from '@/Components/FormLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Radio from '@/Components/Radio.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import Fields from '@/Pages/Campaigns/Fields.vue';
import { PaperAirplaneIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { find } from 'lodash';

const props = defineProps({
    timezones: {
        type: Array,
        required: true,
    },
    sims: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const sendUssdPullsForm = useForm({
    name: null,
    sims: [],
    ussd_codes: '',
    scheduled_at: null,
    timezone: find(props.timezones, ['value', timezone]) ? timezone : props.params.timezone,
    frequency: 1,
    frequency_unit: 'Month',
    recurring: false,
    ends_at: null,
    days_of_week: props.params.days_of_week ?? [1, 2, 3, 4, 5, 6, 7],
    active_hours: {
        start: props.params.active_hours?.start ?? '00:00',
        end: props.params.active_hours?.end ?? '23:59',
    },
    prioritize: false,
    delay: props.params.delay ?? 30,
});

const sendUssdPulls = () => {
    sendUssdPullsForm.transform((data) => ({
        ...data,
        ussd_codes: data.ussd_codes?.split(/\r?\n/),
        scheduled_at: data.scheduled_at
            ? (data.timezone ? data.scheduled_at : new Date(data.scheduled_at).toISOString())
            : null,
        ends_at: data.ends_at
            ? (data.timezone ? data.ends_at : new Date(data.ends_at).toISOString())
            : null,
    })).post(route('ussd-pulls.send'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => sendUssdPullsForm.reset(),
    });
};
</script>

<template>
    <ContentLayout :title="$t('page.send_ussd_pulls')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ $t('page.send_ussd_pulls') }}
                </h1>
            </div>
        </template>

        <form autocomplete="off" @submit.prevent="sendUssdPulls">
            <FormLayout :title="$t('message.campaigns.send')">
                <Fields :form="sendUssdPullsForm" :timezones="timezones">
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="$t('field.sims')" class="sm:mt-px sm:pt-2" for="sims" required />
                        </template>
                        <ComboboxInput id="sims"
                                       v-model="sendUssdPullsForm.sims"
                                       :options="sims"
                                       class="block w-full max-w-lg"
                                       multiple
                                       textAttribute="label" valueAttribute="id" />
                        <InputError :message="sendUssdPullsForm.errors.sims" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendUssdPullsForm.sims.length"
                            :key="index" :message="sendUssdPullsForm.errors[`sims.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="$t('field.codes')" class="sm:mt-px sm:pt-2" for="codes" required />
                        </template>
                        <TextAreaInput id="codes" v-model="sendUssdPullsForm.ussd_codes"
                                       :placeholder="$t('message.send_ussd_pulls.code_per_line')"
                                       class="block w-full max-w-lg"
                                       required />
                        <InputError :message="sendUssdPullsForm.errors.ussd_codes" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendUssdPullsForm.ussd_codes.split(/\r?\n/).length"
                            :key="index" :message="sendUssdPullsForm.errors[`ussd_codes.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="$t('field.delay')" class="sm:mt-px sm:pt-2" for="delay" required />
                        </template>
                        <TextInput id="delay" v-model="sendUssdPullsForm.delay"
                                   class="block w-full max-w-lg"
                                   required
                                   type="text" />
                        <InputError :message="sendUssdPullsForm.errors.delay" class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                                {{ $t('field.prioritize') }} <span class="text-red-600">*</span>
                            </div>
                        </template>
                        <div class="max-w-lg">
                            <p class="text-sm text-gray-500">
                                {{ $t('message.campaigns.prioritize') }}
                            </p>
                            <div class="mt-4 space-y-4">
                                <div class="flex items-center">
                                    <Radio id="prioritize"
                                           v-model="sendUssdPullsForm.prioritize"
                                           :value="true"
                                           name="prioritize"
                                           required />
                                    <InputLabel :value="$t('action.yes')" class="ml-3" for="prioritize" />
                                </div>
                                <div class="flex items-center">
                                    <Radio id="normal"
                                           v-model="sendUssdPullsForm.prioritize"
                                           :value="false"
                                           name="prioritize"
                                           required />
                                    <InputLabel :value="$t('action.no')" class="ml-3" for="normal" />
                                </div>
                                <InputError :message="sendUssdPullsForm.errors.prioritize" class="mt-2" />
                            </div>
                        </div>
                    </FormGroup>
                </Fields>
                <template #actions>
                    <PrimaryButton :class="{ 'opacity-25': sendUssdPullsForm.processing }"
                                   :disabled="sendUssdPullsForm.processing">
                        <PaperAirplaneIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ $t('action.send') }}
                    </PrimaryButton>
                </template>
            </FormLayout>
        </form>
    </ContentLayout>
</template>
