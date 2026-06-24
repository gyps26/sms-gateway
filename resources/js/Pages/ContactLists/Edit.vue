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
import CheckBox from '@/Components/Checkbox.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Radio from '@/Components/Radio.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import List from '@/Pages/ContactLists/List.vue';
import { PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { cloneDeep, map, snakeCase } from 'lodash';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    contactList: {
        type: Object,
        required: true,
    },
    fields: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingField = ref(false);
const fieldBeingDeleted = ref(null);
const fieldBeingUpdated = ref(null);

const managingField = computed({
    get() {
        return creatingField.value || fieldBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                Object.assign(manageFieldForm, cloneDeep(val));
                fieldBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingField.value = val;
        } else {
            creatingField.value = false;
            fieldBeingUpdated.value = null;
            manageFieldForm.reset();
            manageFieldForm.clearErrors();
        }
    },
});

const manageFieldForm = useForm({
    label: null,
    tag: null,
    type: 'text',
    options: [],
    default_value: null,
    required: true,
});

const deleteFieldForm = useForm({});

const columns = [
    { name: t('field.label'), field: 'label' },
    { name: t('field.tag'), field: 'tag' },
    { name: t('field.type'), field: 'type' },
    {
        name: t('field.options'),
        field: 'options',
        render: (options) => map(options, 'value').join(', '),
        sortable: false,
    },
    {
        name: t('field.default_value'),
        field: 'default_value',
        render: (defaultValue) => Array.isArray(defaultValue) ? defaultValue.join(', ') : defaultValue,
    },
    { name: t('field.required'), field: 'required' },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (field) => managingField.value = field,
        screenReader: (field) => t('message.fields.edit', { field: field.label }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (field) => fieldBeingDeleted.value = field,
        screenReader: (field) => t('message.fields.delete', { field: field.label }),
    },
];

const typeOptions = ['text', 'number', 'email', 'dropdown', 'multiselect', 'checkbox', 'radio', 'date', 'datetime-local', 'time', 'textarea'];

const validOptions = computed(() => {
    return manageFieldForm.options.filter(option => option.label && option.value);
});

const manageField = () => {
    if (fieldBeingUpdated.value === null) {
        manageFieldForm.post(route('contact-lists.fields.store', props.contactList), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingField.value = false,
        });
    } else {
        manageFieldForm.put(route('fields.update', fieldBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingField.value = false,
        });
    }
};

const deleteField = () => {
    deleteFieldForm.delete(route('fields.destroy', fieldBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => fieldBeingDeleted.value = null,
    });
};

watch(() => manageFieldForm.type, (value) => {
    if (['dropdown', 'multiselect', 'checkbox', 'radio'].includes(value)) {
        if (manageFieldForm.options.length === 0) {
            manageFieldForm.options.push({ label: '', value: '' });
        }

        if (value === 'multiselect' || value === 'checkbox') {
            if (Array.isArray(manageFieldForm.default_value)) {
                return;
            }

            manageFieldForm.default_value = [];
            return;
        }
    } else {
        manageFieldForm.options = [];
    }

    manageFieldForm.default_value = null;
});

watch(() => manageFieldForm.options, () => {
    if (Array.isArray(manageFieldForm.default_value)) {
        manageFieldForm.default_value = manageFieldForm.default_value.filter((value) => {
            return manageFieldForm.options.some((option) => option.value === value);
        });
    }
}, { deep: true });

watch(() => manageFieldForm.label, (value) => {
    if (value) {
        manageFieldForm.tag = snakeCase(value).toLowerCase();
    }
});
</script>

<template>
    <List :contact-list="contactList" :title="t('page.fields')">
        <template #actions>
            <PrimaryButton type="button" @click="managingField = true;">
                <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                {{ t('action.add') }}
            </PrimaryButton>
        </template>

        <DataTable :actions="actions" :collection="fields" :columns="columns" :only="['fields']" :params="params" />

        <!-- Manage Field Modal -->
        <DialogModal :show="managingField" @close="managingField = false">
            <template #title>
                {{ fieldBeingUpdated ? t('message.fields.edit') : t('message.fields.add') }}
            </template>

            <template #content>
                <form id="manage-field" @submit.prevent="manageField">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.label')" for="label" required />
                            <TextInput id="label" v-model="manageFieldForm.label" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageFieldForm.errors.label" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.tag')" for="tag" required />
                            <TextInput id="tag" v-model="manageFieldForm.tag" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageFieldForm.errors.tag" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.type')" for="type" required />
                            <ComboboxInput id="type" v-model="manageFieldForm.type"
                                           :clearable="false" :hideSearchBox="true" :options="typeOptions"
                                           class="mt-1" />
                            <InputError :message="manageFieldForm.errors.type" class="mt-2" />
                        </div>
                        <div v-if="manageFieldForm.options.length > 0">
                            <PrimaryButton type="button"
                                           @click="manageFieldForm.options.push({ label: '', value: '' })">
                                <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                {{ t('message.fields.action.add_option') }}
                            </PrimaryButton>
                        </div>
                        <div v-for="(option, index) in manageFieldForm.options"
                             class="grid grid-cols-1 gap-3 md:grid-cols-[repeat(3,_1fr)] md:place-items-start">
                            <div>
                                <TextInput v-model="option.label"
                                           :placeholder="t('field.label')"
                                           :title="t('message.fields.option_label', { position: index + 1 })"
                                           required type="text" />
                                <InputError :message="manageFieldForm.errors[`options.${index}.label`]" class="mt-2" />
                            </div>
                            <div>
                                <TextInput v-model="option.value"
                                           :placeholder="t('field.value')"
                                           :title="t('message.fields.option_value', { position: index + 1 })"
                                           required type="text"></TextInput>
                                <InputError :message="manageFieldForm.errors[`options.${index}.value`]" class="mt-2" />
                            </div>
                            <div v-if="manageFieldForm.options.length > 1" class="pt-1">
                                <DangerButton type="button" @click="manageFieldForm.options.splice(index, 1)">
                                    <TrashIcon aria-hidden="true" class="h-4 w-4"></TrashIcon>
                                    <span class="sr-only">
                                    {{ t('message.fields.action.delete_option', { position: index + 1 }) }}
                                </span>
                                </DangerButton>
                            </div>
                            <hr v-if="manageFieldForm.options.length !== index + 1" class="md:hidden">
                        </div>
                        <div>
                            <template v-if="['checkbox', 'radio'].includes(manageFieldForm.type)">
                                <fieldset class="p-4 pb-6 border border-solid border-gray-300 rounded-md">
                                    <legend>{{ t('field.default_value') }}</legend>
                                    <div class="space-y-4">
                                        <div v-for="(option, index) in validOptions"
                                             class="flex items-center">
                                            <Radio v-if="manageFieldForm.type === 'radio'"
                                                   :id="`default-value-option-${index}`"
                                                   v-model="manageFieldForm.default_value" :value="option.value" />
                                            <CheckBox v-else
                                                      :id="`default-value-option-${index}`"
                                                      v-model:checked="manageFieldForm.default_value"
                                                      :value="option.value" />
                                            <InputLabel :for="`default-value-option-${index}`" :value="option.label"
                                                        class="ml-3" />
                                        </div>
                                    </div>
                                </fieldset>
                            </template>
                            <template v-else>
                                <InputLabel :value="t('field.default_value')" for="default-value" />
                                <template
                                    v-if="['text', 'number', 'date', 'datetime-local', 'time', 'email'].includes(manageFieldForm.type)">
                                    <TextInput id="default-value" v-model="manageFieldForm.default_value"
                                               :type="manageFieldForm.type"
                                               class="mt-1 block w-full" />
                                </template>
                                <template v-else-if="manageFieldForm.type === 'textarea'">
                                    <div class="mt-1">
                                        <TextAreaInput id="default-value" v-model="manageFieldForm.default_value"
                                                       class="mt-1 block w-full" />
                                    </div>
                                </template>
                                <template v-else>
                                    <ComboboxInput id="default-value" v-model="manageFieldForm.default_value"
                                                   :multiple="manageFieldForm.type === 'multiselect'"
                                                   :options="validOptions"
                                                   class="mt-1 block w-full" />
                                </template>
                            </template>
                            <InputError :message="manageFieldForm.errors.default_value" class="mt-2" />
                            <InputError
                                v-for="(pos, index) in Array.isArray(manageFieldForm.default_value) ? manageFieldForm.default_value.length: 0"
                                :key="index" :message="manageFieldForm.errors[`default_value.${index}`]"
                                class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput v-model="manageFieldForm.required" :label="t('field.required')" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingField = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageFieldForm.processing }"
                    :disabled="manageFieldForm.processing"
                    class="ml-3"
                    form="manage-field"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Field Confirmation Modal -->
        <ConfirmationModal :show="fieldBeingDeleted != null" @close="fieldBeingDeleted = null">
            <template #title>
                {{ t('message.fields.delete') }}
            </template>

            <template #content>
                {{ t('message.fields.delete_confirmation', { field: fieldBeingDeleted.label }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="fieldBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteFieldForm.processing }"
                    :disabled="deleteFieldForm.processing"
                    class="ml-3"
                    @click="deleteField"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </List>
</template>
