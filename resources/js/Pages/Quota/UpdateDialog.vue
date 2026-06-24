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
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useForm } from '@inertiajs/vue3';
import { find } from 'lodash';
import { computed, watch } from 'vue';

const emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: {
        type: Object,
        default: null,
    },
    timezones: {
        type: Array,
        required: true,
    },
});

const frequencyUnitOptions = useEnums().frequencyUnit;

const proxyModelValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const updateQuotaForm = useForm({
    enabled: false,
    value: 100,
    frequency: 1,
    frequency_unit: 'Day',
    reset_at: null,
    timezone: find(props.timezones, ['value', timezone]) ? timezone : null,
});

const updateQuota = () => {
    updateQuotaForm.transform((data) => ({
        ...data,
        reset_at: data.reset_at
            ? (data.timezone ? data.reset_at : new Date(data.reset_at).toISOString())
            : null,
    })).put(route('quotas.update', proxyModelValue.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => proxyModelValue.value = null,
    });
};

watch(() => proxyModelValue.value, (value) => {
    if (value) {
        Object.assign(updateQuotaForm, { ...value, reset_at: new Date(value.reset_at).toISOString().slice(0, -8) });
    }
});
</script>

<template>
    <DialogModal :show="proxyModelValue != null" @close="proxyModelValue = null">
        <template #title>
            {{ $t('message.quota.edit') }}
        </template>

        <template #content>
            <form id="update-quota" @submit.prevent="updateQuota">
                <div class="space-y-4">
                    <div>
                        <SwitchInput v-model="updateQuotaForm.enabled" :label="$t('field.enabled')" />
                    </div>
                    <div>
                        <InputLabel :required="updateQuotaForm.enabled" :value="$t('field.value')" for="value" />
                        <TextInput id="value"
                                   v-model="updateQuotaForm.value" :disabled="updateQuotaForm.enabled === false"
                                   class="mt-1 block w-full"
                                   min="1"
                                   required
                                   type="number" />
                        <InputError :message="updateQuotaForm.errors.value" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :required="updateQuotaForm.enabled" :value="$t('field.frequency')"
                                    for="frequency" />
                        <TextInput id="frequency"
                                   v-model="updateQuotaForm.frequency" :disabled="updateQuotaForm.enabled === false"
                                   class="mt-1 block w-full"
                                   min="1"
                                   required
                                   type="number" />
                        <InputError :message="updateQuotaForm.errors.frequency" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :required="updateQuotaForm.enabled" :value="$t('field.frequency_unit')"
                                    for="frequency-unit" />
                        <ComboboxInput id="frequency-unit"
                                       v-model="updateQuotaForm.frequency_unit" :clearable="false"
                                       :disabled="updateQuotaForm.enabled === false" :hideSearchBox="true"
                                       :options="frequencyUnitOptions"
                                       class="mt-1" />
                        <InputError :message="updateQuotaForm.errors.frequency_unit" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :value="$t('field.reset_at')" for="reset-at" />
                        <TextInput id="reset-at"
                                   v-model="updateQuotaForm.reset_at"
                                   :disabled="updateQuotaForm.enabled === false" class="mt-1 block w-full"
                                   type="datetime-local" />
                        <InputError :message="updateQuotaForm.errors.reset_at" class="mt-2" />
                    </div>
                    <div v-if="updateQuotaForm.reset_at">
                        <InputLabel :value="$t('field.timezone')" for="timezone" />
                        <ComboboxInput v-model="updateQuotaForm.timezone" :disabled="updateQuotaForm.enabled === false"
                                       :options="timezones"
                                       class="mt-1" />
                        <InputError :message="updateQuotaForm.errors.timezone" class="mt-2" />
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <SecondaryButton @click="proxyModelValue = null">
                {{ $t('action.cancel') }}
            </SecondaryButton>

            <PrimaryButton
                :class="{ 'opacity-25': updateQuotaForm.processing }"
                :disabled="updateQuotaForm.processing"
                class="ml-3"
                form="update-quota"
            >
                {{ $t('action.save') }}
            </PrimaryButton>
        </template>
    </DialogModal>
</template>
