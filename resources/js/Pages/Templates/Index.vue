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
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    templates: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingTemplate = ref(false);
const templateBeingUpdated = ref(null);
const templateBeingDeleted = ref(null);

const managingTemplate = computed({
    get() {
        return creatingTemplate.value || templateBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                Object.assign(manageTemplateForm, val);
                templateBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingTemplate.value = val;
        } else {
            creatingTemplate.value = false;
            templateBeingUpdated.value = null;
            manageTemplateForm.reset();
            manageTemplateForm.clearErrors();
        }
    },
});

const manageTemplateForm = useForm({
    name: null,
    content: null,
});

const deleteTemplateForm = useForm({});

const manageTemplate = () => {
    if (templateBeingUpdated.value !== null) {
        manageTemplateForm.put(route('templates.update', templateBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingTemplate.value = false,
        });
    } else {
        manageTemplateForm.post(route('templates.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingTemplate.value = false,
        });
    }
};

const deleteTemplate = () => {
    deleteTemplateForm.delete(route('templates.destroy', templateBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => templateBeingDeleted.value = null,
    });
};

const columns = [
    { name: t('field.name'), field: 'name' },
    { name: t('field.content'), field: 'content', sortable: false },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (template) => managingTemplate.value = template,
        screenReader: (template) => t('message.templates.action.edit', { template: template.name }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (template) => templateBeingDeleted.value = template,
        screenReader: (template) => t('message.templates.action.delete', { template: template.name }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.templates')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.templates') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingTemplate = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="templates" :columns="columns" :params="params" />

        <!-- Manage Template Modal -->
        <DialogModal :show="managingTemplate" @close="managingTemplate = false">
            <template #title>
                {{ templateBeingUpdated ? t('message.templates.edit') : t('message.templates.add') }}
            </template>

            <template #content>
                <form id="manage-template" @submit.prevent="manageTemplate">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" />
                            <TextInput id="name" v-model="manageTemplateForm.name" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="manageTemplateForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.content')" for="content" />
                            <TextAreaInput id="content" v-model="manageTemplateForm.content"
                                           class="mt-1 block w-full" required />
                            <InputError :message="manageTemplateForm.errors.content" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingTemplate = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageTemplateForm.processing }"
                    :disabled="manageTemplateForm.processing"
                    class="ml-3"
                    form="manage-template"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Template Confirmation Modal -->
        <ConfirmationModal :show="templateBeingDeleted !== null" @close="templateBeingDeleted = null">
            <template #title>
                {{ t('message.templates.delete') }}
            </template>

            <template #content>
                {{ t('message.templates.delete_confirmation', { template: templateBeingDeleted.name }) }}
            </template>

            <template #footer>
                <SecondaryButton @click="templateBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteTemplateForm.processing }"
                    :disabled="deleteTemplateForm.processing"
                    class="ml-3"
                    @click="deleteTemplate"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
