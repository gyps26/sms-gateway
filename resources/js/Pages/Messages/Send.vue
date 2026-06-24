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
import FileInput from "@/Components/FileInput.vue";
import FormGroup from '@/Components/FormGroup.vue';
import FormLayout from '@/Components/FormLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Radio from '@/Components/Radio.vue';
import SpreadsheetUploadInfo from '@/Components/SpreadsheetUploadInfo.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useI18n } from '@/Composables/useI18n.js';
import { useSmsCounter } from '@/Composables/useSmsCounter';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import Fields from '@/Pages/Campaigns/Fields.vue';
import { PaperAirplaneIcon, PlusIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { find } from "lodash";
import { computed, ref, watch } from 'vue';

const props = defineProps({
    timezones: {
        type: Array,
        required: true,
    },
    contactLists: {
        type: Array,
        required: true,
    },
    sims: {
        type: Array,
        required: true,
    },
    templates: {
        type: Object,
        required: true,
    },
    senderIds: {
        type: Object,
        required: true,
    },
    keywords: {
        type: Object,
        required: true,
    },
    maxUploadSize: {
        type: Number,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    }
});

const { t } = useI18n();

const templateInput = ref(null);
const characters = ref(0);
const smsParts = ref(0);

const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

const sendMessagesForm = useForm({
    name: null,
    recipients: props.params.recipients ?? 'mobile_numbers',
    sender_ids: props.params.sender_ids ?? [],
    sims: props.params.sims ?? [],
    mobile_numbers: props.params.mobile_numbers ?? null,
    contact_lists: props.params.contact_lists ?? [],
    spreadsheet: null,
    type: props.params.type ?? 'SMS',
    message: '',
    attachments: null,
    scheduled_at: null,
    timezone: find(props.timezones, ['value', timezone]) ? timezone : props.params.timezone,
    frequency: 1,
    frequency_unit: 'Month',
    recurring: false,
    ends_at: null,
    days_of_week: props.params.days_of_week ?? [1, 2, 3, 4, 5, 6, 7],
    active_hours: {
        start: props.params.active_hours?.start ?? '00:00',
        end: props.params.active_hours?.end ?? '23:59',
    },
    prioritize: false,
    delivery_report: props.params.delivery_report ?? false,
    delay: props.params.delay ?? '2',
});

const recipientOptions = [
    { label: t('message.send_messages.enter_manually'), value: 'mobile_numbers' },
    { label: t('message.send_messages.from_spreadsheet'), value: 'spreadsheet' },
    { label: t('message.send_messages.contact_lists'), value: 'contact_lists' },
];

const types = ['SMS', 'MMS'];

if (Object.values(props.senderIds).filter(el => el.sending_server.supported_types.includes('WhatsApp')).length > 0) {
    types.push('WhatsApp');
}

const senderIdOptions = computed(
    () => Object.values(props.senderIds).filter(el => el.sending_server.supported_types.includes(sendMessagesForm.type)),
);


const count = (text) => {
    const counter = useSmsCounter().count(text);
    characters.value = counter.length;
    smsParts.value = counter.messages;
};

const sendMessages = () => {
    sendMessagesForm.transform((data) => ({
        ...data,
        mobile_numbers: data.recipients === 'mobile_numbers' ? data.mobile_numbers?.split(/\r?\n/).filter(el => el) : null,
        scheduled_at: data.scheduled_at
            ? (data.timezone ? data.scheduled_at : new Date(data.scheduled_at).toISOString())
            : null,
        ends_at: data.ends_at
            ? (data.timezone ? data.ends_at : new Date(data.ends_at).toISOString())
            : null,
    })).post(route('messages.send'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
            sendMessagesForm.reset();
        },
    });
};

const useTemplate = (id) => {
    let content = props.templates[id]?.content ?? '';
    sendMessagesForm.message = content;
    count(content);
};

const addFooter = () => {
    if (sendMessagesForm.recipients === 'contact_lists') {
        sendMessagesForm.message += '\n\n' + t(
            'message.send_messages.unsubscribe_link',
            {
                link: route('contacts.unsubscribe', { contact_list: 1 })
                    .replace('1', '{contact_list}') + '?mobile_number={mobile_number}'
            }
        );
        if (props.keywords.unsubscribe) {
            sendMessagesForm.message += '\n' + t('message.send_messages.or');
            sendMessagesForm.message += '\n' + t('message.send_messages.unsubscribe_text', { unsubscribe: props.keywords.unsubscribe });
        }
    } else {
        if (props.keywords.blacklist) {
            sendMessagesForm.message += '\n' + t('message.send_messages.blacklist', { blacklist: props.keywords.blacklist });
        }
    }
};

watch(() => sendMessagesForm.type, (value, oldValue) => {
    if (value !== oldValue) {
        sendMessagesForm.sender_ids = sendMessagesForm.sender_ids.filter((el) => props.senderIds[el].sending_server.supported_types.includes(value));
        sendMessagesForm.sims = ['SMS', 'MMS'].includes(sendMessagesForm.type) ? sendMessagesForm.sims : [];
    }
});
</script>

<template>
    <ContentLayout :title="t('page.send_messages')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.send_messages') }}
                </h1>
            </div>
        </template>

        <!--
            Unlike other browsers, Firefox by default persists the dynamic checked state of an <input> across page loads.
            We can use the autocomplete attribute to control this feature.
            This feature results into serious issue with radio button state on using "Reopen Closed Tab" feature.
            Directly adding autocomplete onto the radio input didn't work that's why using using a form tag and disabling autocomplete on whole form.
        -->
        <form autocomplete="off" @submit.prevent="sendMessages">
            <FormLayout :title="t('message.campaigns.send')">
                <Fields :form="sendMessagesForm" :timezones="timezones">
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="t('field.type')" class="sm:mt-px sm:pt-2" for="type" required />
                        </template>
                        <div class="space-y-4">
                            <div v-for="type in types" :key="type" class="flex items-center">
                                <Radio :id="type.toLowerCase()" v-model="sendMessagesForm.type" :value="type"
                                       required />
                                <label :for="type.toLowerCase()"
                                       class="ml-3 block text-sm font-medium text-gray-700">{{ type }}</label>
                            </div>
                        </div>
                        <InputError :message="sendMessagesForm.errors.type" class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :required="sendMessagesForm.sims.length < 1" :value="t('field.sender_ids')"
                                        class="sm:mt-px sm:pt-2"
                                        for="sender-ids" />
                        </template>
                        <ComboboxInput id="sender-ids" v-model="sendMessagesForm.sender_ids"
                                       :options="senderIdOptions"
                                       class="block w-full"
                                       multiple
                                       textAttribute="value" valueAttribute="id" />
                        <InputError :message="sendMessagesForm.errors.sender_ids" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendMessagesForm.sender_ids.length"
                            :key="index" :message="sendMessagesForm.errors[`sender_ids.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup v-if="['SMS', 'MMS'].includes(sendMessagesForm.type)">
                        <template #label>
                            <InputLabel :required="sendMessagesForm.sender_ids.length < 1" :value="t('field.sims')"
                                        class="sm:mt-px sm:pt-2"
                                        for="sims" />
                        </template>
                        <ComboboxInput id="sims"
                                       v-model="sendMessagesForm.sims"
                                       :options="sims"
                                       class="block w-full"
                                       multiple
                                       textAttribute="label" valueAttribute="id" />
                        <InputError :message="sendMessagesForm.errors.sims" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendMessagesForm.sims.length"
                            :key="index" :message="sendMessagesForm.errors[`sims.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="t('field.recipients')" class="sm:mt-px sm:pt-2" for="recipients"
                                        required />
                        </template>
                        <ComboboxInput id="recipients" v-model="sendMessagesForm.recipients"
                                       :clearable="false" :hideSearchBox="true"
                                       :options="recipientOptions" class="block w-full" />
                        <InputError :message="sendMessagesForm.errors.recipients" class="mt-2" />
                    </FormGroup>
                    <FormGroup v-if="sendMessagesForm.recipients === 'mobile_numbers'">
                        <template #label>
                            <InputLabel :value="t('field.mobile_numbers')" class="sm:mt-px sm:pt-2" for="mobile-numbers"
                                        required />
                        </template>
                        <TextAreaInput id="mobile-numbers" v-model="sendMessagesForm.mobile_numbers" required />
                        <p class="mt-2 text-sm text-gray-500">{{ t('message.mobile_number_per_line') }}</p>
                        <InputError :message="sendMessagesForm.errors.mobile_numbers" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendMessagesForm.mobile_numbers.split(/\r?\n/).length"
                            :key="index" :message="sendMessagesForm.errors[`mobile_numbers.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup v-if="sendMessagesForm.recipients === 'spreadsheet'">
                        <template #label>
                            <InputLabel :value="t('field.spreadsheet')" class="sm:mt-px sm:pt-2" for="spreadsheet"
                                        required />
                        </template>
                        <SpreadsheetUploadInfo :max-upload-size="maxUploadSize" class="p-4 mb-4" sample="../sample.csv">
                            {{ t('message.send_messages.upload_spreadsheet_info') }}
                        </SpreadsheetUploadInfo>
                        <FileInput id="spreadsheet" v-model="sendMessagesForm.spreadsheet"
                                   accept=".csv,.ods,.tsv,.xls,.xlsx"
                                   class="mt-2 block w-full"
                                   required />
                        <InputError :message="sendMessagesForm.errors.spreadsheet" class="mt-2" />
                    </FormGroup>
                    <FormGroup v-if="sendMessagesForm.recipients === 'contact_lists'">
                        <template #label>
                            <InputLabel :value="t('field.contact_lists')" class="sm:mt-px sm:pt-2" for="contact-lists"
                                        required />
                        </template>
                        <ComboboxInput id="contact-lists" v-model="sendMessagesForm.contact_lists"
                                       :options="contactLists" class="block w-full"
                                       multiple
                                       textAttribute="name"
                                       valueAttribute="id" />
                        <InputError :message="sendMessagesForm.errors.contact_lists" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendMessagesForm.contact_lists.length"
                            :key="index" :message="sendMessagesForm.errors[`contact_lists.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :value="t('field.template')" class="sm:mt-px sm:pt-2" for="template" />
                        </template>
                        <ComboboxInput id="template" ref="templateInput"
                                       :options="Object.values(templates)"
                                       class="block w-full"
                                       textAttribute="name"
                                       valueAttribute="id"
                                       @change="useTemplate" />
                    </FormGroup>
                    <FormGroup>
                        <template #label>
                            <InputLabel :required="sendMessagesForm.type === 'SMS' || sendMessagesForm.attachments < 1"
                                        :value="t('field.message')" class="sm:mt-px sm:pt-2"
                                        for="message" />
                        </template>
                        <div v-if="sendMessagesForm.recipients === 'contact_lists' || props.keywords.blacklist"
                             class="flex justify-end">
                            <PrimaryButton class="mb-3" type="button" @click="addFooter">
                                <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                                {{ t('message.send_messages.add_footer') }}
                            </PrimaryButton>
                        </div>
                        <TextAreaInput id="message" v-model="sendMessagesForm.message"
                                       :required="sendMessagesForm.type === 'SMS' || sendMessagesForm.attachments === null"
                                       @update:modelValue="count" />
                        <div class="mt-2 flex justify-end text-sm text-gray-500">
                            <div v-if="sendMessagesForm.type === 'SMS'" class="space-x-2">
                                <span>{{ t('message.send_messages.characters') }}: <strong>{{ characters }}</strong></span>
                                <span>{{ t('message.send_messages.sms_parts') }}: <strong>{{ smsParts }}</strong></span>
                            </div>
                        </div>
                        <InputError :message="sendMessagesForm.errors.message" class="mt-2" />
                    </FormGroup>
                    <FormGroup v-if="sendMessagesForm.type !== 'SMS'">
                        <template #label>
                            <InputLabel :value="t('field.attachments')" class="sm:mt-px sm:pt-2" for="attachments" />
                        </template>
                        <FileInput id="attachments" v-model="sendMessagesForm.attachments"
                                   accept=".jpg,.jpeg,.png,.gif,.aac,.3gp,.amr,.mp3,.m4a,.wav,.mp4,.txt,.vcf,.html"
                                   class="block w-full"
                                   multiple />
                        <InputError :message="sendMessagesForm.errors.attachments" class="mt-2" />
                        <InputError
                            v-for="(pos, index) in sendMessagesForm.attachments ? sendMessagesForm.attachments.length : 0"
                            :key="index" :message="sendMessagesForm.errors[`attachments.${index}`]"
                            class="mt-2" />
                    </FormGroup>
                    <template v-if="sendMessagesForm.sims?.length > 0">
                        <FormGroup>
                            <template #label>
                                <InputLabel :value="t('field.delay')" class="sm:mt-px sm:pt-2" for="delay" required />
                            </template>
                            <TextInput id="delay" v-model="sendMessagesForm.delay"
                                       class="block w-full"
                                       required
                                       type="text" />
                            <InputError :message="sendMessagesForm.errors.delay" class="mt-2" />
                        </FormGroup>
                        <FormGroup v-if="sendMessagesForm.type === 'SMS'">
                            <template #label>
                                <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                                    {{ t('field.delivery_report') }} <span class="text-red-600">*</span>
                                </div>
                            </template>
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ t('message.send_messages.delivery_report_question') }}
                                </p>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-center">
                                        <Radio id="request-delivery-report"
                                               v-model="sendMessagesForm.delivery_report"
                                               :value="true"
                                               name="delivery_report"
                                               required />
                                        <InputLabel :value="t('action.yes')" class="ml-3"
                                                    for="request-delivery-report" />
                                    </div>
                                    <div class="flex items-center">
                                        <Radio id="no-delivery-report"
                                               v-model="sendMessagesForm.delivery_report"
                                               :value="false"
                                               name="delivery_report"
                                               required />
                                        <InputLabel :value="t('action.no')" class="ml-3" for="no-delivery-report" />
                                    </div>
                                    <InputError :message="sendMessagesForm.errors.delivery_report" class="mt-2" />
                                </div>
                            </div>
                        </FormGroup>
                        <FormGroup>
                            <template #label>
                                <div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700">
                                    {{ t('field.prioritize') }} <span class="text-red-600">*</span>
                                </div>
                            </template>
                            <div>
                                <p class="text-sm text-gray-500">
                                    {{ t('message.campaigns.prioritize') }}
                                </p>
                                <div class="mt-4 space-y-4">
                                    <div class="flex items-center">
                                        <Radio id="prioritize"
                                               v-model="sendMessagesForm.prioritize"
                                               :value="true"
                                               name="prioritize"
                                               required />
                                        <InputLabel :value="t('action.yes')" class="ml-3" for="prioritize" />
                                    </div>
                                    <div class="flex items-center">
                                        <Radio id="normal"
                                               v-model="sendMessagesForm.prioritize"
                                               :value="false"
                                               name="prioritize"
                                               required />
                                        <InputLabel :value="t('action.no')" class="ml-3" for="normal" />
                                    </div>
                                    <InputError :message="sendMessagesForm.errors.prioritize" class="mt-2" />
                                </div>
                            </div>
                        </FormGroup>
                    </template>
                </Fields>
                <template #actions>
                    <PrimaryButton :class="{ 'opacity-25': sendMessagesForm.processing }"
                                   :disabled="sendMessagesForm.processing">
                        <PaperAirplaneIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                        {{ t('action.send') }}
                    </PrimaryButton>
                </template>
            </FormLayout>
        </form>
    </ContentLayout>
</template>
