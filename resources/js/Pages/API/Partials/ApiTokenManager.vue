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
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionMessage from '@/Components/ActionMessage.vue';
import ActionSection from '@/Components/ActionSection.vue';
import Checkbox from '@/Components/Checkbox.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    tokens: Array,
    availablePermissions: Array,
    defaultPermissions: Array,
});

const createApiTokenForm = useForm({
    name: '',
    permissions: props.defaultPermissions,
});

const updateApiTokenForm = useForm({
    permissions: [],
});

const deleteApiTokenForm = useForm({});

const displayingToken = ref(false);
const managingPermissionsFor = ref(null);
const apiTokenBeingDeleted = ref(null);

const createApiToken = () => {
    createApiTokenForm.post(route('api-tokens.store'), {
        preserveScroll: true,
        onSuccess: () => {
            displayingToken.value = true;
            createApiTokenForm.reset();
        },
    });
};

const manageApiTokenPermissions = (token) => {
    updateApiTokenForm.permissions = token.abilities;
    managingPermissionsFor.value = token;
};

const updateApiToken = () => {
    updateApiTokenForm.put(route('api-tokens.update', managingPermissionsFor.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (managingPermissionsFor.value = null),
    });
};

const confirmApiTokenDeletion = (token) => {
    apiTokenBeingDeleted.value = token;
};

const deleteApiToken = () => {
    deleteApiTokenForm.delete(route('api-tokens.destroy', apiTokenBeingDeleted.value), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => (apiTokenBeingDeleted.value = null),
    });
};
</script>

<template>
    <div>
        <!-- Generate API Token -->
        <FormSection @submitted="createApiToken">
            <template #title>
                {{ $t('message.api_token.create.title') }}
            </template>

            <template #description>
                {{ $t('message.api_token.create.description') }}
            </template>

            <template #form>
                <!-- Token Name -->
                <div class="col-span-6">
                    <InputLabel for="name" :value="$t('field.name')" />
                    <TextInput
                        id="name"
                        v-model="createApiTokenForm.name"
                        type="text"
                        class="mt-1 block w-full"
                        autofocus
                    />
                    <InputError :message="createApiTokenForm.errors.name" class="mt-2" />
                </div>

                <!-- Token Permissions -->
                <div v-if="availablePermissions.length > 0" class="col-span-6">
                    <InputLabel for="permissions" :value="$t('field.permissions')" />

                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="permission in availablePermissions" :key="permission">
                            <label class="flex items-center">
                                <Checkbox v-model:checked="createApiTokenForm.permissions" :value="permission" />
                                <span class="ms-2 text-sm text-gray-600">{{ permission }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            </template>

            <template #actions>
                <ActionMessage :on="createApiTokenForm.recentlySuccessful" class="me-3">
                    {{ $t('message.created') }}
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': createApiTokenForm.processing }" :disabled="createApiTokenForm.processing">
                    {{ $t('action.create') }}
                </PrimaryButton>
            </template>
        </FormSection>

        <div v-if="tokens.length > 0">
            <SectionBorder />

            <!-- Manage API Tokens -->
            <div class="mt-10 sm:mt-0">
                <ActionSection>
                    <template #title>
                        {{ $t('message.api_token.manage.title') }}
                    </template>

                    <template #description>
                        {{ $t('message.api_token.manage.description') }}
                    </template>

                    <!-- API Token List -->
                    <template #content>
                        <div class="space-y-6">
                            <div v-for="token in tokens" :key="token.id" class="flex items-center justify-between">
                                <div class="break-all">
                                    {{ token.name }}
                                </div>

                                <div class="flex items-center ms-2">
                                    <div v-if="token.last_used_ago" class="text-sm text-gray-400">
                                        {{ $t('message.api_token.manage.last_used', { time: token.last_used_ago }) }}
                                    </div>

                                    <button
                                        v-if="availablePermissions.length > 0"
                                        class="cursor-pointer ms-6 text-sm text-gray-400 underline"
                                        @click="manageApiTokenPermissions(token)"
                                    >
                                        {{ $t('field.permissions') }}
                                    </button>

                                    <button class="cursor-pointer ms-6 text-sm text-red-500" @click="confirmApiTokenDeletion(token)">
                                        {{ $t('action.delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </ActionSection>
            </div>
        </div>

        <!-- Token Value Modal -->
        <DialogModal :show="displayingToken" @close="displayingToken = false">
            <template #title>
                {{ $t('message.api_token.display.title') }}
            </template>

            <template #content>
                <div>
                    {{ $t('message.api_token.display.description') }}
                </div>

                <div v-if="$page.props.jetstream.flash.token" class="mt-4 bg-gray-100 px-4 py-2 rounded font-mono text-sm text-gray-500 break-all">
                    {{ $page.props.jetstream.flash.token }}
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="displayingToken = false">
                    {{ $t('action.close') }}
                </SecondaryButton>
            </template>
        </DialogModal>

        <!-- API Token Permissions Modal -->
        <DialogModal :show="managingPermissionsFor != null" @close="managingPermissionsFor = null">
            <template #title>
                {{ $t('message.api_token.permissions.title') }}
            </template>

            <template #content>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div v-for="permission in availablePermissions" :key="permission">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="updateApiTokenForm.permissions" :value="permission" />
                            <span class="ms-2 text-sm text-gray-600">{{ permission }}</span>
                        </label>
                    </div>
                </div>
            </template>

            <template #footer>
                <SecondaryButton @click="managingPermissionsFor = null">
                    {{ $t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    class="ms-3"
                    :class="{ 'opacity-25': updateApiTokenForm.processing }"
                    :disabled="updateApiTokenForm.processing"
                    @click="updateApiToken"
                >
                    {{ $t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>

        <!-- Delete Token Confirmation Modal -->
        <ConfirmationModal :show="apiTokenBeingDeleted != null" @close="apiTokenBeingDeleted = null">
            <template #title>
                {{ $t('message.api_token.delete.title') }}
            </template>

            <template #content>
                {{ $t('message.api_token.delete.description') }}
            </template>

            <template #footer>
                <SecondaryButton @click="apiTokenBeingDeleted = null">
                    {{ $t('action.cancel') }}
                </SecondaryButton>

                <DangerButton
                    class="ms-3"
                    :class="{ 'opacity-25': deleteApiTokenForm.processing }"
                    :disabled="deleteApiTokenForm.processing"
                    @click="deleteApiToken"
                >
                    {{ $t('action.delete') }}
                </DangerButton>
            </template>
        </ConfirmationModal>
    </div>
</template>
