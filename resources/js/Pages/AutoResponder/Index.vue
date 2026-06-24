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
import FileInput from "@/Components/FileInput.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PencilSquareIcon, PlusIcon, TrashIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { cloneDeep } from 'lodash';
import { computed, ref } from 'vue';

const props = defineProps({
    autoResponses: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingAutoResponse = ref(false);
const autoResponseBeingUpdated = ref(null);
const autoResponseBeingDeleted = ref(null);

const managingAutoResponse = computed({
    get() {
        return creatingAutoResponse.value || autoResponseBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                manageAutoResponseForm._method = 'put';
                Object.assign(manageAutoResponseForm, cloneDeep(val), {
                    attachments: null,
                    uploaded: cloneDeep(val.attachments),
                });
                autoResponseBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingAutoResponse.value = val;
        } else {
            creatingAutoResponse.value = false;
            autoResponseBeingUpdated.value = null;
            manageAutoResponseForm.reset();
            manageAutoResponseForm.clearErrors();
        }
    },
});

const manageAutoResponseForm = useForm({
    _method: 'post',
    messages: [''],
    response: null,
    type: 'SMS',
    attachments: null,
    uploaded: null,
    criterion: 'Match',
    enabled: true,
});

const deleteTemplateForm = useForm({});

const manageAutoResponse = () => {
    if (autoResponseBeingUpdated.value !== null) {
        manageAutoResponseForm.post(route('auto-responses.update', autoResponseBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingAutoResponse.value = false,
        });
    } else {
        manageAutoResponseForm.post(route('auto-responses.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingAutoResponse.value = false,
        });
    }
};

const deleteAutoResponse = () => {
    deleteTemplateForm.delete(route('auto-responses.destroy', autoResponseBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => autoResponseBeingDeleted.value = null,
    });
};

const onTypeChange = (value) => {
    if (value === 'SMS') {
        manageAutoResponseForm.uploaded = null;
        manageAutoResponseForm.attachments = null;
    } else {
        if (autoResponseBeingUpdated.value !== null) {
            manageAutoResponseForm.uploaded = cloneDeep(autoResponseBeingUpdated.value.attachments);
        }
    }
};

const criterionOptions = useEnums().criterion;

const columns = [
    { name: t('field.messages'), field: 'messages', sortable: false },
    { name: t('field.response'), field: 'response', sortable: false },
    { name: t('field.type'), field: 'type' },
    {
        name: t('field.attachments'),
        field: 'attachments',
        sortable: false,
        render: (attachments) => {
            let result = '';
            if (attachments) {
                attachments.forEach(function (attachment) {
                    result += `<a href="${ attachment.url }" class="text-indigo-600 hover:text-indigo-900" target="_blank">${ attachment.file_name }</a>, `;
                });
            }
            return result.slice(0, -2);
        },
    },
    {
        name: t('field.criterion'),
        field: 'criterion',
        render: (criterion) => criterionOptions[criterion],
    },
    { name: t('field.enabled'), field: 'enabled' },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (autoResponse) => managingAutoResponse.value = autoResponse,
        screenReader: (autoResponse) => t('message.auto_responder.action.edit', { id: autoResponse.id }),
    },
    {
        name: t('action.delete'),
        icon: TrashIcon,
        callback: (autoResponse) => autoResponseBeingDeleted.value = autoResponse,
        screenReader: (autoResponse) => t('message.auto_responder.action.delete', { id: autoResponse.id }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.auto_responder')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.auto_responder') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingAutoResponse = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="autoResponses" :columns="columns" :params="params" />

        <!-- Manage Auto Response Modal -->
        <DialogModal :show="managingAutoResponse" @close="managingAutoResponse = false">
            <template #title>
                {{ autoResponseBeingUpdated ? t('message.auto_responder.edit') : t('message.auto_responder.add') }}
            </template>

            <template #content>
                <form id="manage-auto-response" @submit.prevent="manageAutoResponse">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.messages')" required />
                            <div class="mt-1 space-y-2">
                                <div v-for="(message, index) in manageAutoResponseForm.messages"
                                     :key="index" class="flex space-x-3">
                                    <div class="flex-1">
                                        <TextAreaInput v-model="manageAutoResponseForm.messages[index]"
                                                       :title="t('message.auto_responder.message', { position: index + 1 })"
                                                       autofocus
                                                       class="w-full"
                                                       required
                                                       rows="2" />
                                        <InputError :message="manageAutoResponseForm.errors[`messages.${index}`]" class="mt-2" />
                                    </div>
                                    <div v-if="index !== 0">
                                        <DangerButton type="button"
                                                      @click="manageAutoResponseForm.messages.splice(index, 1)">
                                            <TrashIcon aria-hidden="true" class="h-4 w-4" />
                                            <span class="sr-only">
                                            {{ t('message.auto_responder.deleteMessage', { position: index + 1 }) }}
                                        </span>
                                        </DangerButton>
                                    </div>
                                    <div v-else>
                                        <PrimaryButton type="button" @click="manageAutoResponseForm.messages.push('')">
                                            <PlusIcon aria-hidden="true" class="h-4 w-4" />
                                            <span class="sr-only">{{ t('message.auto_responder.addMessage') }}</span>
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <InputLabel :value="t('field.response')" for="response" required />
                            <TextAreaInput id="response" v-model="manageAutoResponseForm.response"
                                           class="mt-1 block w-full" required />
                            <InputError :message="manageAutoResponseForm.errors.response" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.type')" for="type" required />
                            <ComboboxInput v-model="manageAutoResponseForm.type" :clearable="false"
                                           :hideSearchBox="true" :options="['SMS', 'MMS']" class="mt-1"
                                           @change="onTypeChange" />
                            <InputError :message="manageAutoResponseForm.errors.type" class="mt-2" />
                        </div>
                        <div v-if="manageAutoResponseForm.type === 'MMS'">
                            <InputLabel :value="t('field.attachments')" for="attachments" />
                            <FileInput id="attachments" v-model="manageAutoResponseForm.attachments"
                                       accept=".jpg,.jpeg,.png,.gif,.aac,.3gp,.amr,.mp3,.m4a,.wav,.mp4,.txt,.vcf"
                                       class="mt-1 block w-full"
                                       multiple />
                            <InputError :message="manageAutoResponseForm.errors.attachments" class="mt-2" />
                            <InputError
                                v-for="(pos, index) in manageAutoResponseForm.attachments ? manageAutoResponseForm.attachments.length : 0"
                                :key="index" :message="manageAutoResponseForm.errors[`attachments.${index}`]"
                                class="mt-2" />
                        </div>
                        <div v-if="manageAutoResponseForm.uploaded !== null" class="space-y-2">
                            <div v-for="(media, index) in manageAutoResponseForm.uploaded"
                                 :key="media.id" class="flex space-x-3 text-sm">
                                <a :href="media.url" class="text-indigo-600 hover:text-indigo-900"
                                   target="_blank">
                                    {{ media.file_name }}
                                </a>
                                <a :title="t('action.delete')" class="text-red-600 hover:text-red-900"
                                   href="#"
                                   @click.prevent="manageAutoResponseForm.uploaded.splice(index, 1)">
                                    <TrashIcon aria-hidden="true" class="-ml-0.5 mr-2 h-5 w-5" />
                                </a>
                            </div>
                        </div>
                        <div>
                            <InputLabel :value="t('field.criterion')" for="criterion" required />
                            <ComboboxInput id="criterion"
                                           v-model="manageAutoResponseForm.criterion"
                                           :clearable="false"
                                           :hideSearchBox="true" :options="criterionOptions"
                                           class="mt-1" />
                            <InputError :message="manageAutoResponseForm.errors.criterion" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput v-model="manageAutoResponseForm.enabled" :label="t('field.enabled')" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingAutoResponse = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageAutoResponseForm.processing }"
                    :disabled="manageAutoResponseForm.processing"
                    class="ml-3"
                    form="manage-auto-response"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>


        <!-- Delete Auto Response Confirmation Modal -->
        <ConfirmationModal :show="autoResponseBeingDeleted !== null" @close="autoResponseBeingDeleted = null">
            <template #title>
                {{ t('message.auto_responder.delete') }}
            </template>

            <template #content>
                {{ t('message.auto_responder.delete_confirmation') }}
            </template>

            <template #footer>
                <SecondaryButton @click="autoResponseBeingDeleted = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    :class="{ 'opacity-25': deleteTemplateForm.processing }"
                    :disabled="deleteTemplateForm.processing"
                    class="ml-3"
                    @click="deleteAutoResponse"
                >
                    {{ t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </ContentLayout>
</template>
