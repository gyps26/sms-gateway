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
import ChatBubble from "@/Components/ChatBubble.vue";
import DialogModal from "@/Components/DialogModal.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useGroupBy } from "@/Composables/useGroupBy.js";
import { useI18n } from "@/Composables/useI18n.js";
import ContentLayout from "@/Layouts/ContentLayout.vue";
import MessageableFilter from "@/Pages/Messages/Partials/MessageableFilter.vue";
import { Listbox, ListboxButton, ListboxLabel, ListboxOption, ListboxOptions } from "@headlessui/vue";
import { ChatBubbleBottomCenterTextIcon, CheckBadgeIcon, PaperClipIcon, PencilSquareIcon } from "@heroicons/vue/20/solid";
import { router, useForm, usePoll } from "@inertiajs/vue3";
import { find, get, isNull, omitBy } from 'lodash';
import { computed, nextTick, onMounted, reactive, ref, watch } from "vue";

const props = defineProps({
    messages: {
        type: Array,
        default: [],
    },
    phoneId: {
        type: String,
        default: null,
    },
    sims: {
        type: Array,
        required: true,
    },
    senderIds: {
        type: Array,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    }
});

const { t } = useI18n();

const sim = find(props.sims, { 'id': props.params.sim });
const senderId = find(props.senderIds, { 'id': props.params.sender_id });
const types = props.params.sim ? ['SMS', 'MMS'] : (senderId ? senderId.sending_server.supported_types : ['SMS', 'MMS', 'WhatsApp']);

const form = useForm({
    mobile_number: props.params.mobile_number,
    message: null,
    sim: props.params.sim,
    sender_id: props.params.sender_id,
    attachments: null,
    type: types[0],
    delivery_report: true,
});

const creatingNewChat = ref(false);
const attachmentsInput = ref(null);
const messagesContainer = ref(null);

const scrollToBottom = () => {
    messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
};

const createNewChatForm = reactive({
    mobile_number: null,
    sim: props.sims.length > 0 ? props.sims[0].id : null,
    sender_id: props.sims.length === 0 && props.senderIds.length > 0 ? props.senderIds[0].id : null,
})

const sendMessage = () => {
    form.post(route('chat.send'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            form.reset();
            router.reload({ only: ['messages'] });
        }
    });
};

const collection = computed(() =>
    useGroupBy().nest(
        props.messages,
        [(i) => new Date(i.status === 'Received' ? i.delivered_at : i.sent_at).toISOString().split('T')[0]]
    )
);

const title = computed(() => {
    if (props.params.mobile_number) {
        if (props.phoneId) {
            return t('message.chat.title_alt', {
                phoneId: props.phoneId,
                mobileNumber: props.params.mobile_number,
                sender: get(sim, 'label') ?? get(senderId, 'value')
            });
        } else {
            return t('message.chat.title', {
                mobileNumber: props.params.mobile_number,
                sender: get(sim, 'label') ?? get(senderId, 'value')
            });
        }
    } else {
        return t('page.chat')
    }
});

const disabled = computed(() => {
    let result = form.processing || isNull(form.mobile_number) || form.mobile_number.trim().length === 0 || (isNull(form.sim) && isNull(form.sender_id));

    if (form.type === 'SMS') {
        return result || isNull(form.message) || form.message.trim().length === 0;
    } else {
        return result || ((isNull(form.message) || form.message.trim().length === 0) && (isNull(form.attachments) || form.attachments.length === 0));
    }
});

const createNewChat = () => {
    router.visit('chat', { data: omitBy(createNewChatForm, isNull) });
};

watch(() => form.type, (type) => form.defaults('type', type));

usePoll(10000, { only: ['messages'] });

onMounted(scrollToBottom);

watch(
    () => props.messages,
    (value, oldValue) => {
        if (value.length <= 0) {
            return;
        }

        if (oldValue.length === 0 || value.at(-1).id !== oldValue.at(-1).id) {
            nextTick(scrollToBottom);
        }
    }
);
</script>

<template>
    <ContentLayout :title="title">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ title }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingNewChat = true">
                    <PencilSquareIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.new') }}
                </PrimaryButton>
            </div>
        </template>

        <!-- Chat area -->
        <div class="flex flex-col mx-4 gap-4" style="height: calc(100vh - 18rem);">
            <!-- Messages -->
            <div ref="messagesContainer" class="flex-1 min-h-[400px] overflow-y-auto">
                <div v-for="(data, date) in collection" :key="date" class="space-y-4">
                    <div class="flex justify-center">
                        <div class="bg-gray-900 text-white px-2 py-1 rounded-md">{{ date }}</div>
                    </div>
                    <ChatBubble v-for="message in data" :key="message.id" :message="message" />
                </div>
            </div>

            <!-- Input -->
            <footer class="h-24 flex items-center border-t">
                <form action="#" class="relative w-full mt-24">
                    <div
                        class="rounded-lg bg-white overflow-hidden outline outline-1 -outline-offset-1 outline-gray-300 focus-within:outline focus-within:outline-2 focus-within:-outline-offset-2 focus-within:outline-indigo-200/50">
                        <label class="sr-only" for="message">{{ t('field.message') }}</label>
                        <textarea id="message" v-model="form.message"
                                  :placeholder="t('message.chat.message')"
                                  class="block w-full resize-none px-3 pt-2.5 text-base text-gray-900 placeholder:text-gray-400 focus:outline focus:outline-0 sm:text-sm/6"
                                  name="message"
                                  rows="2" style="border: none; box-shadow: none;" />

                        <!-- Spacer element to match the height of the toolbar -->
                        <div aria-hidden="true">
                            <div class="py-2">
                                <div class="h-9" />
                            </div>
                            <div class="h-px" />
                            <div class="py-2">
                                <div class="py-px">
                                    <div class="h-9" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute inset-x-px bottom-0">
                        <!-- Actions: These are just examples to demonstrate the concept, replace/wire these up however makes sense for your project. -->
                        <div class="flex flex-nowrap justify-end space-x-2 px-2 py-2 sm:px-3">
                            <Listbox v-if="form.type === types[0]" as="div" v-model="form.delivery_report" class="shrink-0">
                                <ListboxLabel class="sr-only">{{ t('field.delivery_report') }}</ListboxLabel>
                                <div class="relative">
                                    <ListboxButton class="relative inline-flex items-center whitespace-nowrap rounded-full bg-gray-50 px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 sm:px-3">
                                        <CheckBadgeIcon :class="[form.delivery_report ? 'text-gray-500' : 'text-gray-300', 'size-5 shrink-0 sm:-ml-1']" aria-hidden="true" />
                                        <span :class="[form.delivery_report ? 'text-gray-900' : '', 'hidden truncate sm:ml-2 sm:block']">{{ t('field.delivery_report') }}</span>
                                    </ListboxButton>

                                    <transition leave-active-class="transition ease-in duration-100" leave-from-class="" leave-to-class="opacity-0">
                                        <ListboxOptions class="absolute bottom-10 right-0 z-10 mt-1 max-h-56 w-52 overflow-auto rounded-lg bg-white py-3 text-base shadow outline outline-1 outline-black/5 sm:text-sm">
                                            <ListboxOption as="template" v-for="option in [{ name: 'ON', value: true }, { name: 'OFF', value: false }]" :key="option.value" :value="option.value" v-slot="{ active }">
                                                <li :class="[active ? 'relative bg-gray-100 hover:outline-none' : 'bg-white', 'cursor-default select-none px-3 py-2']">
                                                    <div class="flex items-center">
                                                        <span class="block truncate font-medium">{{ option.name }}</span>
                                                    </div>
                                                </li>
                                            </ListboxOption>
                                        </ListboxOptions>
                                    </transition>
                                </div>
                            </Listbox>

                            <Listbox v-model="form.type" as="div" class="shrink-0">
                                <ListboxLabel class="sr-only">{{ t('message.chat.type') }}</ListboxLabel>
                                <div class="relative">
                                    <ListboxButton
                                        class="relative inline-flex items-center whitespace-nowrap rounded-full bg-gray-50 px-2 py-2 text-sm font-medium text-gray-500 hover:bg-gray-100 sm:px-3">
                                        <ChatBubbleBottomCenterTextIcon
                                            :class="[form.type === null ? 'text-gray-300' : 'text-gray-500', 'size-5 shrink-0 sm:-ml-1']"
                                            aria-hidden="true" />
                                        <span
                                            :class="[form.type === null ? '' : 'text-gray-900', 'hidden truncate sm:ml-2 sm:block']">
                                            {{ form.type === null ? 'Label' : form.type }}
                                        </span>
                                    </ListboxButton>

                                    <transition leave-active-class="transition ease-in duration-100"
                                                leave-from-class="" leave-to-class="opacity-0">
                                        <ListboxOptions
                                            class="absolute bottom-10 right-0 z-10 mt-1 max-h-56 w-52 overflow-auto rounded-lg bg-white py-3 text-base shadow outline outline-1 outline-black/5 sm:text-sm">
                                            <ListboxOption v-for="type in types" :key="type" v-slot="{ active }"
                                                           :value="type" as="template">
                                                <li :class="[active ? 'relative bg-gray-100 hover:outline-none' : 'bg-white', 'cursor-default select-none px-3 py-2']">
                                                    <div class="flex items-center">
                                                        <span class="block truncate font-medium">{{ type }}</span>
                                                    </div>
                                                </li>
                                            </ListboxOption>
                                        </ListboxOptions>
                                    </transition>
                                </div>
                            </Listbox>
                        </div>

                        <div
                            class="flex items-center justify-between space-x-3 border-t border-gray-200 px-2 py-2 sm:px-3">
                            <div class="flex">
                                <input
                                    ref="attachmentsInput"
                                    class="hidden"
                                    multiple
                                    type="file"
                                    @change="form.attachments = $event.target.files"
                                >
                                <button v-if="form.type !== types[0]"
                                        class="group -my-2 -ml-2 inline-flex items-center rounded-full px-3 py-2 text-left text-gray-400"
                                        type="button"
                                        @click="attachmentsInput.click()">
                                    <PaperClipIcon aria-hidden="true"
                                                   class="-ml-1 mr-2 size-5 group-hover:text-gray-500" />
                                    <span
                                        class="text-sm italic text-gray-500 group-hover:text-gray-600">
                                        {{ form.attachments ? Array.from(form.attachments).map(f => f.name).join(', ') : t('message.chat.attachments') }}
                                    </span>
                                </button>
                            </div>
                            <div class="shrink-0">
                                <button
                                    :class="{ 'opacity-25': disabled }"
                                    :disabled="disabled"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                                    type="submit"
                                    @click="sendMessage">
                                    {{ t('action.send') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </footer>
        </div>

        <!-- Create New Chat Modal -->
        <DialogModal :show="creatingNewChat" @close="creatingNewChat = false">
            <template #title>
                {{ t('message.chat.new') }}
            </template>

            <template #content>
                <form id="create-new-chat" @submit.prevent="createNewChat">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.mobile_number')" for="mobile-number" />
                            <TextInput id="mobile-number" v-model="createNewChatForm.mobile_number" autofocus
                                       class="mt-1 block w-full"
                                       type="text" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.sim')" for="sim" />
                            <MessageableFilter
                                v-model:senderId="createNewChatForm.sender_id"
                                v-model:sim="createNewChatForm.sim"
                                :clearable="false"
                                :sender-ids="senderIds" :sims="sims" class="mt-1 block w-full" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="creatingNewChat = null">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    class="ml-3"
                    form="create-new-chat"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
