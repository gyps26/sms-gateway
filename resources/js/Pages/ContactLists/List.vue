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
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Tabs from '@/Components/Tabs.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import {
    CogIcon,
    DocumentArrowDownIcon,
    DocumentArrowUpIcon,
    PencilSquareIcon,
    UsersIcon,
} from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    contactList: {
        type: Object,
        required: true,
    },
    title: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();

const updatingContactList = ref(false);

const updateContactListForm = useForm({
    name: null,
});

const tabs = [
    {
        name: t('page.contacts'),
        icon: UsersIcon,
        href: route('contact-lists.contacts.index', props.contactList),
        current: route().current('contact-lists.contacts.index', props.contactList),
    },
    {
        name: t('page.fields'),
        icon: CogIcon,
        href: route('contact-lists.edit', props.contactList),
        current: route().current('contact-lists.edit', props.contactList),
    },
    {
        name: t('action.import'),
        icon: DocumentArrowDownIcon,
        href: route('contacts.import', props.contactList),
        current: route().current('contacts.import', props.contactList),
    },
    {
        name: t('action.export'),
        icon: DocumentArrowUpIcon,
        href: route('contacts.export', props.contactList),
        current: route().current('contacts.export', props.contactList),
    },
];

const updateContactList = () => {
    updateContactListForm.put(route('contact-lists.update', props.contactList.id), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => updatingContactList.value = false,
    });
};
</script>

<template>
    <ContentLayout :title="`${contactList.name}:${title}`">
        <template #header>
            <div class="flex-1 min-w-0">
                <div class="flex items-center">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ contactList.name }}
                    </h1>
                    <a class="text-indigo-600 hover:text-indigo-900"
                       href="#" title="Edit"
                       @click.prevent="updatingContactList = true; updateContactListForm.name = contactList.name;">
                        <PencilSquareIcon aria-hidden="true" class="-mr-0.5 ml-2 h-5 w-5" />
                    </a>
                </div>
            </div>
        </template>

        <div class="px-4 sm:px-6 lg:mx-auto lg:px-8">
            <div class="relative border-b border-gray-200 pb-5 sm:pb-0">
                <div class="md:flex md:items-center md:justify-between">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">{{ title }}</h3>
                    <div class="mt-3 flex md:absolute md:top-3 md:right-0 md:mt-0">
                        <slot name="actions" />
                    </div>
                </div>
                <div class="mt-4">
                    <Tabs :tabs="tabs"></Tabs>
                </div>
            </div>
        </div>

        <div class="mt-8">
            <slot />
        </div>

        <!-- Edit Contact List Modal -->
        <DialogModal :show="updatingContactList" @close="updatingContactList = false">
            <template #title>
                {{ t('message.contact_lists.edit') }}
            </template>

            <template #content>
                <form id="update-contact-list" @submit.prevent="updateContactList">
                    <InputLabel :value="t('field.name')" for="name" />
                    <TextInput id="name" v-model="updateContactListForm.name" autofocus class="mt-1 block w-full"
                               required type="text" />
                    <InputError :message="updateContactListForm.errors.name" class="mt-2" />
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="updatingContactList = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': updateContactListForm.processing }"
                    :disabled="updateContactListForm.processing"
                    class="ml-3"
                    form="update-contact-list"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
