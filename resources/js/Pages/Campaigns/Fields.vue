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
import Checkbox from '@/Components/Checkbox.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import FormGroup from '@/Components/FormGroup.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Radio from '@/Components/Radio.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from '@/Composables/useEnums.js';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    timezones: {
        type: Array,
        required: true,
    },
});

const frequencyUnitOptions = useEnums().frequencyUnit;
const daysOfWeek = useEnums().daysOfWeek;
</script>

<template>
    <FormGroup>
        <template #label>
            <InputLabel :value="$t('field.name')" class="sm:mt-px sm:pt-2" for="name" />
        </template>
        <TextInput id="name" v-model="form.name" class="block w-full" type="text" />
        <InputError :message="form.errors.name" class="mt-2" />
    </FormGroup>

    <slot />

    <FormGroup>
        <template #label>
            <InputLabel :value="$t('field.schedule')" class="sm:mt-px sm:pt-2" for="schedule" />
        </template>
        <TextInput id="schedule" v-model="form.scheduled_at" class="block w-full" type="datetime-local" />
        <InputError :message="form.errors.scheduled_at" class="mt-2" />
    </FormGroup>

    <FormGroup>
        <template #label>
            <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                {{ $t('field.recurring') }} <span class="text-red-600">*</span>
            </div>
        </template>
        <div>
            <p class="text-sm text-gray-500">
                {{ $t('message.campaigns.recurring') }}
            </p>
            <div class="mt-4 space-y-4">
                <div class="flex items-center">
                    <Radio id="repeat"
                           v-model="form.recurring"
                           :value="true"
                           name="recurring"
                           required />
                    <InputLabel :value="$t('action.yes')" class="ml-3" for="repeat" />
                </div>
                <div class="flex items-center">
                    <Radio id="one-off"
                           v-model="form.recurring"
                           :value="false"
                           name="recurring"
                           required />
                    <InputLabel :value="$t('action.no')" class="ml-3" for="one-off" />
                </div>
                <InputError :message="form.errors.recurring" class="mt-2" />
            </div>
        </div>
    </FormGroup>

    <template v-if="form.recurring">
        <FormGroup>
            <template #label>
                <InputLabel :value="$t('field.frequency')" class="sm:mt-px sm:pt-2" for="frequency"
                            required />
            </template>
            <div class="flex flex-row space-x-2">
                <div class="block w-full">
                    <TextInput id="frequency" v-model="form.frequency"
                               min="1"
                               required
                               type="number" />
                    <InputError :message="form.errors.frequency" class="mt-2" />
                </div>
                <div class="block w-full">
                    <ComboboxInput id="unit"
                                   v-model="form.frequency_unit" :clearable="false"
                                   :hideSearchBox="true"
                                   :options="frequencyUnitOptions" />
                    <InputError :message="form.errors.frequency_unit" class="mt-2" />
                </div>
            </div>
        </FormGroup>
        <FormGroup>
            <template #label>
                <InputLabel :value="$t('field.ends_at')" class="sm:mt-px sm:pt-2" for="ends-at" />
            </template>
            <TextInput id="ends-at" v-model="form.ends_at"
                       class="block w-full" type="datetime-local" />
            <InputError :message="form.errors.ends_at" class="mt-2" />
        </FormGroup>
    </template>

    <FormGroup>
        <template #label>
            <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                {{ $t('field.active_hours') }} <span class="text-red-600">*</span>
            </div>
        </template>
        <div class="flex flex-row items-center space-x-2">
            <div class="block w-full">
                <TextInput v-model="form.active_hours.start"
                           class="w-full"
                           required
                           type="time" />
                <InputError :message="form.errors['active_hours.start']" class="mt-2" />
            </div>
            <div class="block w-full">
                <TextInput v-model="form.active_hours.end"
                           class="w-full"
                           required
                           type="time" />
                <InputError :message="form.errors['active_hours.end']" class="mt-2" />
            </div>
        </div>
    </FormGroup>

    <FormGroup>
        <template #label>
            <InputLabel :value="$t('field.timezone')" class="sm:mt-px sm:pt-2" for="timezone" required />
        </template>
        <ComboboxInput v-model="form.timezone"
                       :clearable="false" :options="timezones" class="block w-full" />
        <InputError :message="form.errors.timezone" class="mt-2" />
    </FormGroup>

    <FormGroup>
        <template #label>
            <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                {{ $t('field.days_of_week') }} <span class="text-red-600">*</span>
            </div>
        </template>
        <p class="text-sm text-gray-500">
            {{ $t('message.campaigns.days_of_week') }}
        </p>
        <div class="mt-4 space-y-4">
            <div v-for="(day, value) in daysOfWeek" :key="day" class="flex items-center">
                <Checkbox :id="day" v-model:checked="form.days_of_week"
                          :value="parseInt(value)" />
                <InputLabel :for="day" :value="day" class="ml-3" />
            </div>
            <InputError :message="form.errors.days_of_week" class="mt-2" />
        </div>
    </FormGroup>
</template>