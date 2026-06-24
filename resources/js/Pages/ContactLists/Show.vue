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
import { PaperAirplaneIcon, PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    contactList: {
        type: Object,
        required: true,
    },
    contacts: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingContact = ref(false);
const contactBeingUpdated = ref(null);

const managingContact = computed({
    get() {
        return creatingContact.value || contactBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                manageContactForm.mobile_number = val.mobile_number;
                manageContactForm.subscribed = val.subscribed;

                props.contactList.fields.forEach((field) => {
                    manageContactForm[field.tag] = val.fields[field.id]?.value;
                });

                contactBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingContact.value = val;
        } else {
            creatingContact.value = false;
            contactBeingUpdated.value = null;
            manageContactForm.reset();
            manageContactForm.clearErrors();
        }
    },
});

const fields = {};

const columns = [
    { name: t('field.mobile_number'), field: 'mobile_number' },
    { name: t('field.subscribed'), field: 'subscribed' },
];

props.contactList.fields.forEach((field) => {
    columns.push({
        name: field.label,
        field: 'fields',
        render: (fields) => {
            const contactField = fields[field.id];
            if (contactField) {
                return Array.isArray(contactField.value) ? contactField.value.join(', ') : contactField.value;
            } else {
                return field.default_value;
            }
        },
        sortable: false,
    });

    fields[field.tag] = field.default_value;
});

columns.push(
    { name: t('field.created_at'), field: 'created_at', render: (created_at) => new Date(created_at).toLocaleString() },
    { name: t('field.updated_at'), field: 'updated_at', render: (updated_at) => new Date(updated_at).toLocaleString() },
);

const manageContactForm = useForm({
    mobile_number: null,
    subscribed: true,
    ...fields,
});

const deleteContactsForm = useForm({
    ids: [],
    all: false,
    search: null,
});

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (contact) => managingContact.value = contact,
        screenReader: (contact) => t('message.contacts.action.edit', { contact: contact.mobile_number }),
    },
    {
        name: t('action.send'),
        icon: PaperAirplaneIcon,
        callback: (contact) => router.visit(
            route('messages.create', {
                recipients: 'mobile_numbers',
                mobile_numbers: [contact.mobile_number],
            })
        ),
        screenReader: (contact) => t('message.contacts.action.edit', { contact: contact.mobile_number }),
    },
];

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(deleteContactsForm, params),
        },
    ],
];

const manageContact = () => {
    if (contactBeingUpdated.value === null) {
        manageContactForm.post(route('contact-lists.contacts.store', props.contactList), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingContact.value = false,
        });
    } else {
        manageContactForm.put(route('contacts.update', contactBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingContact.value = false,
        });
    }
};

const deleteContacts = () => {
    deleteContactsForm.post(route('contacts.delete', props.contactList.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteContactsForm.reset(),
    });
};
</script>

<template>
    <List :contact-list="contactList" :title="t('page.contacts')">
        <template #actions>
            <PrimaryButton type="button" @click="managingContact = true">
                <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                {{ t('action.add') }}
            </PrimaryButton>
        </template>

        <DataTable :actions="actions" :bulk-actions="bulkActions" :collection="contacts" :columns="columns"
                   :only="['contacts']"
                   :params="params" />

        <!-- Manage Contact Modal -->
        <DialogModal :show="managingContact" @close="managingContact = false">
            <template #title>
                {{ contactBeingUpdated ? t('message.contacts.edit') : t('message.contacts.add') }}
            </template>

            <template #content>
                <form id="mange-contact" @submit.prevent="manageContact">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.mobile_number')" for="mobile-number" required />
                            <TextInput id="mobile-number" v-model="manageContactForm.mobile_number" autofocus
                                       class="mt-1 block w-full"
                                       required
                                       type="text" />
                            <InputError :message="manageContactForm.errors.mobile_number" class="mt-2" />
                        </div>
                        <div v-for="field in contactList.fields" :key="field.id">
                            <template v-if="['checkbox', 'radio'].includes(field.type)">
                                <fieldset class="p-4 pb-6 border border-solid border-gray-300 rounded-md">
                                    <legend>{{ field.label }} <span v-if="field.required" class="text-red-600">*</span></legend>
                                    <div class="space-y-4">
                                        <div v-for="(option, index) in field.options" :key="option.value"
                                             class="flex items-center">
                                            <Radio v-if="field.type === 'radio'"
                                                   :id="`${field.tag}-option-${index}`"
                                                   v-model="manageContactForm[field.tag]"
                                                   :name="field.tag" :required="field.required"
                                                   :value="option.value" />
                                            <CheckBox v-else
                                                      :id="`${field.tag}-option-${index}`"
                                                      v-model:checked="manageContactForm[field.tag]"
                                                      :value="option.value" />
                                            <InputLabel :for="`${field.tag}-option-${index}`" :value="option.label"
                                                        class="ml-3" />
                                        </div>
                                    </div>
                                </fieldset>
                            </template>
                            <template v-else>
                                <InputLabel :for="field.tag" :required="field.required" :value="field.label" />
                                <template
                                    v-if="['text', 'number', 'date', 'datetime-local', 'time', 'email'].includes(field.type)">
                                    <TextInput :id="field.tag" v-model="manageContactForm[field.tag]"
                                               :required="field.required"
                                               :type="field.type"
                                               class="mt-1 block w-full" />
                                </template>
                                <template v-else-if="field.type === 'textarea'">
                                    <div class="mt-1">
                                        <TextAreaInput :id="field.tag" v-model="manageContactForm[field.tag]"
                                                       :required="field.required"
                                                       class="mt-1 block w-full" />
                                    </div>
                                </template>
                                <template v-else>
                                    <ComboboxInput :id="field.tag" v-model="manageContactForm[field.tag]"
                                                   :multiple="field.type === 'multiselect'"
                                                   :options="field.options"
                                                   class="mt-1 block w-full" />
                                </template>
                            </template>
                            <InputError :message="manageContactForm.errors[field.tag]" class="mt-2" />
                            <InputError
                                v-for="(pos, index) in Array.isArray(manageContactForm[field.tag]) ? manageContactForm[field.tag].length: 0"
                                :key="index" :message="manageContactForm.errors[`${field.tag}.${index}`]"
                                class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput v-model="manageContactForm.subscribed" :label="t('field.subscribed')" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingContact = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageContactForm.processing }"
                    :disabled="manageContactForm.processing"
                    class="ml-3"
                    form="mange-contact"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Contacts Confirmation Modal -->
        <ConfirmationModal :show="deleteContactsForm.ids.length > 0" @close="deleteContactsForm.reset()">
            <template #title>
                {{ t('message.contacts.delete', deleteContactsForm.ids.length) }}
            </template>

            <template #content>
                {{ t('message.contacts.delete_confirmation', deleteContactsForm.ids.length) }}
            </template>

            <template #footer>
                <SecondaryButton @click="deleteContactsForm.reset()">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteContactsForm.processing }"
                    :disabled="deleteContactsForm.processing"
                    class="ml-3"
                    @click="deleteContacts"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </List>
</template>
