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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    taxes: {
        type: Object,
        required: true,
    },
    countries: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingTax = ref(false);
const taxBeingUpdated = ref(null);
const taxBeingDeleted = ref(null);

const queryParams = reactive({
    country: props.params.country,
});

const managingTax = computed({
    get() {
        return creatingTax.value || taxBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                Object.assign(manageTaxForm, val);
                taxBeingUpdated.value = val;
            } else if (typeof val === 'boolean') {
                creatingTax.value = val;
            }
        } else {
            creatingTax.value = false;
            taxBeingUpdated.value = null;
            manageTaxForm.reset();
            manageTaxForm.clearErrors();
        }
    },
});

const manageTaxForm = useForm({
    name: null,
    rate: null,
    country: props.countries[0].value,
    inclusive: false,
    enabled: true,
});

const deleteTaxForm = useForm({});

const manageTax = () => {
    if (taxBeingUpdated.value !== null) {
        manageTaxForm.put(route('taxes.update', taxBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingTax.value = false,
        });
    } else {
        manageTaxForm.post(route('taxes.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingTax.value = false,
        });
    }
};

const columns = [
    { name: t('field.name'), field: 'name' },
    { name: t('field.rate'), field: 'rate' },
    {
        name: t('field.country'),
        field: 'country',
        render: (country) => props.countries.find(c => c.value === country).label,
    },
    { name: t('field.inclusive'), field: 'inclusive' },
    { name: t('field.enabled'), field: 'enabled' },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (tax) => managingTax.value = tax,
        screenReader: (tax) => t('message.taxes.action.edit', { tax: tax.name }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (tax) => taxBeingDeleted.value = tax,
        screenReader: (tax) => t('message.taxes.action.delete', { tax: tax.name }),
    },
];

const deleteTax = () => {
    deleteTaxForm.delete(route('taxes.destroy', taxBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => taxBeingDeleted.value = null,
    });
};

watch(queryParams, () => useQueryFilter(queryParams).refresh(['taxes']));
</script>

<template>
    <ContentLayout :title="t('page.taxes')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.taxes') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingTax = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <div class="bg-white rounded-lg shadow m-8">
            <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <InputLabel :value="t('field.country')" for="country" />
                        <ComboboxInput id="country" v-model="queryParams.country"
                                       :options="countries"
                                       class="mt-1 block w-full" />
                    </div>
                </div>
            </div>
        </div>

        <DataTable :actions="actions" :collection="taxes" :columns="columns" :only="['taxes']" :params="params" />

        <!-- Manage Tax Modal -->
        <DialogModal :show="managingTax" @close="managingTax = false">
            <template #title>
                {{ taxBeingUpdated ? t('message.taxes.edit') : t('message.taxes.add') }}
            </template>

            <template #content>
                <form id="manage-tax" @submit.prevent="manageTax">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" required />
                            <TextInput id="name" v-model="manageTaxForm.name" autofocus class="mt-1 block w-full"
                                       required />
                            <InputError :message="manageTaxForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.rate')" for="rate" required />
                            <TextInput id="rate" v-model="manageTaxForm.rate" class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageTaxForm.errors.rate" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.country')" for="country" required />
                            <ComboboxInput
                                id="country"
                                v-model="manageTaxForm.country"
                                :clearable="false"
                                :options="countries"
                                class="mt-1 block w-full" />
                            <InputError :message="manageTaxForm.errors.country" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput id="inclusive" v-model="manageTaxForm.inclusive" :label="t('field.inclusive')"
                                         class="mt-1 block w-full" />
                            <InputError :message="manageTaxForm.errors.inclusive" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput id="enabled" v-model="manageTaxForm.enabled" :label="t('field.enabled')"
                                         class="mt-1 block w-full" />
                            <InputError :message="manageTaxForm.errors.enabled" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingTax = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageTaxForm.processing }"
                    :disabled="manageTaxForm.processing"
                    class="ml-3"
                    form="manage-tax"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Tax Confirmation Modal -->
        <ConfirmationModal :show="taxBeingDeleted != null" @close="taxBeingDeleted = null">
            <template #title>
                {{ t('message.taxes.delete') }}
            </template>

            <template #content>
                {{ t('message.taxes.delete_confirmation', { tax: taxBeingDeleted.name }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="taxBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteTaxForm.processing }"
                    :disabled="deleteTaxForm.processing"
                    class="ml-3"
                    @click="deleteTax"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
