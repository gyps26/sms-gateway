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
import Attachments from "@/Components/Attachments.vue";
import Checkbox from '@/Components/Checkbox.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DateRangeFilter from '@/Components/DateRangeFilter.vue';
import DropDown from '@/Components/DropDown.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Pagination from '@/Components/Pagination.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SelectionMenu from '@/Components/SelectionMenu.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useGroupBy } from "@/Composables/useGroupBy.js";
import { useI18n } from '@/Composables/useI18n.js';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import Export from '@/Pages/Messages/Partials/Export.vue';
import Item from '@/Pages/Messages/Partials/Item.vue';
import MessageableFilter from '@/Pages/Messages/Partials/MessageableFilter.vue';
import {
    ArrowPathRoundedSquareIcon,
    ArrowUturnLeftIcon,
    CalendarDaysIcon,
    ChatBubbleLeftRightIcon,
    CheckBadgeIcon,
    CheckIcon,
    ClockIcon,
    CpuChipIcon,
    DocumentChartBarIcon,
    ExclamationCircleIcon,
    ExclamationTriangleIcon,
    InboxArrowDownIcon,
    PhoneIcon,
    RectangleStackIcon,
    TagIcon,
    TrashIcon,
    XMarkIcon,
} from '@heroicons/vue/20/solid';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { debounce, get, map, omit } from 'lodash';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    messages: {
        type: Object,
        required: true,
    },
    users: {
        type: Object,
        required: true,
    },
    campaign: {
        type: Object,
        default: null,
    },
    exportStatus: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const perPageOptions = [10, 25, 50, 100];

const defaults = {
    'campaign': props.campaign?.id,
    'per_page': perPageOptions[0],
    'user': props.campaign ? props.campaign.user_id : (usePage().props.auth.user.is_admin ? null : usePage().props.auth.user.id),
};

const queryParams = reactive({
    'campaign': defaults.campaign ?? props.params.campaign,
    'user': defaults.user ?? props.params.user,
    'sim': props.params.sim,
    'sender_id': props.params.sender_id,
    'mobile_number': props.params.mobile_number,
    'message': props.params.message,
    'type': props.params.type,
    'statuses': props.params.statuses,
    'after': props.params.after,
    'before': props.params.before,
    'per_page': props.params.per_page ?? defaults.per_page,
});

const selected = ref([]);
const selectAll = ref(false);

const deleteMessagesForm = useForm({
    ids: [],
    all: false,
    ...queryParams,
});

const retryMessagesForm = useForm({
    ids: [],
    all: false,
    ...queryParams,
});

const statusBadges = {
    'Pending': {
        icon: ClockIcon,
        iconBackground: 'bg-yellow-500',
        badgeColor: 'text-yellow-800',
        badgeBackground: 'bg-yellow-100',
    },
    'Queued': {
        icon: RectangleStackIcon,
        iconBackground: 'bg-blue-500',
        badgeColor: 'text-blue-800',
        badgeBackground: 'bg-blue-100',
    },
    'Processed': {
        icon: ArrowPathRoundedSquareIcon,
        iconBackground: 'bg-cyan-500',
        badgeColor: 'text-cyan-800',
        badgeBackground: 'bg-cyan-100',
    },
    'Sent': {
        icon: CheckIcon,
        iconBackground: 'bg-green-500',
        badgeColor: 'text-green-800',
        badgeBackground: 'bg-green-100',
    },
    'Delivered': {
        icon: CheckBadgeIcon,
        iconBackground: 'bg-green-500',
        badgeColor: 'text-green-800',
        badgeBackground: 'bg-green-100',
    },
    'Failed': {
        icon: XMarkIcon,
        iconBackground: 'bg-red-500',
        badgeColor: 'text-red-800',
        badgeBackground: 'bg-red-100',
    },
    'Received': {
        icon: InboxArrowDownIcon,
        iconBackground: 'bg-indigo-500',
        badgeColor: 'text-indigo-800',
        badgeBackground: 'bg-indigo-100',
    },
};

const statusOptions = props.campaign ? omit(useEnums().messageStatus, ['Received']) : useEnums().messageStatus;

const typeOptions = ['SMS', 'MMS', 'WhatsApp'];

const bulkActions = [
    [
        {
            name: t('action.delete'),
            icon: TrashIcon,
            callback: (params) => Object.assign(
                deleteMessagesForm, params, queryParams
            ),
        },
        {
            name: t('action.retry'),
            icon: ArrowPathRoundedSquareIcon,
            callback: (params) => Object.assign(
                retryMessagesForm, params, queryParams
            ),
        }
    ],
];

const groupName = (userId, messageable) => {
    const items = messageable.split(':');
    const messageableId = items[1];
    const user = props.users[userId];

    let label = items[0] === 'sim' ? `SIM #${ messageableId }` : `Sender ID #${ messageableId }`;

    if (user) {
        if (items[0] === 'sim' && user.sims[messageableId]) {
            label = user.sims[messageableId].label;
        } else if (items[0] === 'sender_id' && user.sender_ids[messageableId]) {
            label = user.sender_ids[messageableId].value
        }
    }

    if (props.params.user || props.campaign || props.params.campaign) {
        return label;
    } else {
        return user ? `${ user.email } (${ label })` : `User #${ userId } (${ label })`;
    }
};

const groupRoute = (userId, messageable) => {
    const split = messageable.split(':');
    if (split[0] === 'sim') {
        return route(route().current(), { ...route().params, user: userId, sim: split[1] });
    } else {
        return route(route().current(), { ...route().params, user: userId, sender_id: split[1] });
    }
};

const replyRoute = (message) => {
    return route('messages.create', {
        recipients: 'mobile_numbers',
        type: message.type,
        mobile_numbers: [to(message)],
        sims: message.messageable_type === 'sim' ? [message.messageable_id] : null,
        sender_ids: message.messageable_type === 'sender_id' ? [message.messageable_id] : null,
    });
};

const chatRoute = (message) => {
    return route('chat', {
        mobile_number: to(message),
        sim: message.messageable_type === 'sim' ? message.messageable_id : null,
        sender_id: message.messageable_type === 'sender_id' ? message.messageable_id : null,
    });
};

const collection = computed(() => useGroupBy().nest(
    props.messages.data,
    [
        (i) => new Date(i.sent_at).toISOString().split('T')[0],
        'user_id',
        (i) => `${ i.messageable_type }:${ i.messageable_id }`,
    ],
));

const sims = computed(() => {
    const user = get(props.users, queryParams.user);
    return user ? Object.values(user.sims) : [];
});

const senderIds = computed(() => {
    const user = get(props.users, queryParams.user);
    return user ? Object.values(user.sender_ids) : [];
});

const isValidMobileNumber = (value) => {
    return !isNaN(value.replace('+', ''));
};

const from = (message) => {
    // TODO: show the sender ID or SIM label when to or from is not available
    return message.status === 'Received' ? message.to : message.from;
};

const to = (message) => {
    return message.status === 'Received' ? message.from : message.to;
};

const deleteMessages = () => {
    deleteMessagesForm.post(route('messages.delete'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => deleteMessagesForm.reset(),
    });
};

const retryMessages = () => {
    retryMessagesForm.post(route('messages.retry'), {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => retryMessagesForm.reset(),
    });
};

const only = ['messages', 'params'];

const refresh = () => useQueryFilter(queryParams, defaults).refresh(only);

watch(() => [queryParams.per_page, queryParams.after, queryParams.before, queryParams.user, queryParams.sim, queryParams.sender_id, queryParams.statuses, queryParams.type], refresh);
watch(() => [queryParams.mobile_number, queryParams.message], debounce(refresh, 300));
watch(() => queryParams.user, (value, oldValue) => {
    if (value !== oldValue) {
        queryParams.sim = null;
        queryParams.sender_id = null;
    }
});
</script>

<template>
    <div class="bg-white rounded-lg shadow m-5">
        <div class="border-b border-gray-200 px-4 py-5 sm:px-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div v-if="$page.props.auth.user.is_admin && campaign === null">
                    <InputLabel :value="t('entity.user')" for="user" />
                    <ComboboxInput id="user" v-model="queryParams.user"
                                   :options="Object.values(users)"
                                   class="mt-1 block w-full"
                                   value-attribute="id" />
                </div>
                <div>
                    <InputLabel :value="t('field.sender/receiver')" for="messageable" />
                    <MessageableFilter
                        v-model:senderId="queryParams.sender_id"
                        v-model:sim="queryParams.sim"
                        :sender-ids="senderIds" :sims="sims" class="mt-1 block w-full" />
                </div>
                <div>
                    <InputLabel :value="t('field.mobile_number')" for="mobile-number" />
                    <TextInput id="mobile-number" v-model="queryParams.mobile_number"
                               class="mt-1 block w-full"
                               type="text" />
                </div>
                <div>
                    <InputLabel :value="t('field.message')" for="message" />
                    <TextAreaInput id="message" v-model="queryParams.message" class="mt-1 block w-full"
                                   rows="1" />
                </div>
                <div v-if="campaign == null">
                    <InputLabel :value="t('field.type')" for="type" />
                    <ComboboxInput id="type" v-model="queryParams.type" :hideSearchBox="true" :options="typeOptions"
                                   class="mt-1 block w-full" />
                </div>
                <div>
                    <InputLabel :value="t('field.statuses')" for="statuses" />
                    <ComboboxInput id="statuses" v-model="queryParams.statuses" :hideSearchBox="true"
                                   :options="statusOptions"
                                   class="mt-1 block w-full"
                                   multiple />
                </div>
                <DateRangeFilter v-model:after="queryParams.after" v-model:before="queryParams.before" />
            </div>
        </div>
        <div class="divide-y divide-gray-200 px-4 py-5 sm:px-6">
            <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
                <div class="ml-4">
                    <Export :params="queryParams" />
                </div>
                <div class="flex-shrink-0">
                    <ComboboxInput v-model="queryParams.per_page" :clearable="false" :hideSearchBox="true"
                                   :options="perPageOptions"
                                   class="w-24" />
                </div>
            </div>
        </div>
    </div>

    <div class="flow-root m-5">
        <ul v-if="props.messages.data.length > 0" class="mb-8" role="list">
            <Item>
                <template #badge>
                    <div class="flex items-center justify-center space-x-4 h-8">
                        <span
                            class="inline-flex space-x-1 rounded-md p-2 font-normal leading-5 bg-gray-500 text-white">
                            <SelectionMenu v-model:select-all="selectAll" v-model:selected="selected"
                                           :ids="map(props.messages.data, 'id')"
                                           as="divs" class="w-5 h-5" />
                        </span>
                        <DropDown v-if="selected.length > 0" :items="bulkActions"
                                  @select="(item) => item.callback({'ids': selected, 'all': selectAll})">
                                <span class="bg-gray-500 text-white px-1 mr-1 rounded-sm">
                                    {{ selectAll ? messages.meta.total : selected.length }}
                                </span>
                            {{ t('action.bulk_actions') }}
                        </DropDown>
                    </div>
                </template>
            </Item>
            <template v-for="(data, sentDate) in collection">
                <Item>
                    <template #badge>
                        <Link
                            :href="route(route().current(), { ...route().params, after: sentDate, before: sentDate })"
                            class="inline-flex space-x-1 rounded-md p-2 font-normal leading-5 bg-indigo-500 text-white">
                            <CalendarDaysIcon aria-hidden="true" class="h-5 w-5" />
                            <span>{{ new Date(sentDate).toLocaleDateString() }}</span>
                        </Link>
                    </template>
                </Item>
                <template v-for="(userData, userId) in data">
                    <template v-for="(messageableData, messageable) in userData">
                        <Item v-if="! (props.params.sim || props.params.sender_id)">
                            <template #badge>
                                <Link
                                    :href="groupRoute(userId, messageable)"
                                    class="inline-flex space-x-1 rounded-md p-2 font-normal leading-5 bg-violet-500 text-white">
                                    <CpuChipIcon v-if="messageable.startsWith('sim')" aria-hidden="true"
                                                 class="h-5 w-5 text-white" />
                                    <TagIcon v-else aria-hidden="true" class="h-5 w-5 text-white" />
                                    <span>{{ groupName(userId, messageable) }}</span>
                                </Link>
                            </template>
                        </Item>
                        <Item v-for="message in messageableData" :key="message.id">
                            <template #badge>
                                <span
                                    :class="[statusBadges[message.status].iconBackground, 'h-8 w-8 rounded-full flex items-center justify-center']">
                                    <Component :is="statusBadges[message.status].icon"
                                               aria-hidden="true" class="h-5 w-5 text-white" />
                                </span>
                            </template>

                            <div class="flex-1 bg-white overflow-hidden shadow rounded-md">
                                <div class="py-4 px-6">
                                    <div class="flex flex-wrap justify-between">
                                        <div class="min-w-0">
                                            <div class="flex items-center space-x-2">
                                                <Checkbox v-model:checked="selected" :value="message.id" />
                                                <p class="text-sm font-bold text-gray-900">
                                                    <Link
                                                        :href="route(route().current(), { ...route().params, mobile_number: to(message) })"
                                                        class="hover:underline">
                                                        {{ message.phone_id ? `${message.phone_id} (${to(message)})` : to(message) }}
                                                    </Link>
                                                </p>
                                                <template
                                                    v-if="message.user_id === usePage().props.auth.user.id && isValidMobileNumber(to(message))">
                                                    <a :href="`tel:${to(message)}`"
                                                       class="text-indigo-600 hover:text-indigo-900">
                                                        <PhoneIcon aria-hidden="true" class="h-5 w-5" />
                                                    </a>
                                                    <Link :href="chatRoute(message)"
                                                          class="text-indigo-600 hover:text-indigo-900">
                                                        <ChatBubbleLeftRightIcon aria-hidden="true" class="h-5 w-5" />
                                                    </Link>
                                                    <Link v-if="message.status === 'Received'"
                                                          :href="replyRoute(message)"
                                                          class="text-indigo-600 hover:text-indigo-900">
                                                        <ArrowUturnLeftIcon aria-hidden="true" class="h-5 w-5" />
                                                    </Link>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <p class="ml-6 text-sm text-gray-500">
                                                {{ new Date(message.sent_at).toLocaleTimeString() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="py-4 px-6 text-gray-600 whitespace-pre-wrap space-y-4">
                                    <p v-if="message.content">
                                        {{ message.content }}
                                    </p>
                                    <Attachments :attachments="message.attachments" />
                                </div>
                                <hr>
                                <div class="flex items-center flex-wrap py-4 px-6 gap-2">
                                    <template
                                        v-for="badge in [
                                            { icon: ExclamationTriangleIcon, text: message.error },
                                            { icon: TagIcon, text: message.type },
                                            { icon: ExclamationCircleIcon, text: statusOptions[message.status] },
                                            { icon: DocumentChartBarIcon, text: from(message) },
                                            {
                                                icon: ClockIcon,
                                                text: message.delivered_at
                                                        ? ((delivered_at = new Date(message.delivered_at)).toLocaleDateString() === sentDate ? delivered_at.toLocaleTimeString(): delivered_at.toLocaleString())
                                                        : null
                                            }
                                        ]">
                                        <span
                                            v-if="badge.text"
                                            :class="[statusBadges[message.status].badgeColor, statusBadges[message.status].badgeBackground]"
                                            class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium leading-5">
                                            <component :is="badge.icon"
                                                       aria-hidden="true"
                                                       class="h-4 w-4 mr-1" />
                                            {{ badge.text }}
                                        </span>
                                    </template>
                                </div>
                            </div>
                        </Item>
                    </template>
                </template>
            </template>
        </ul>
        <div v-else class="mt-8 text-2xl flex justify-center items-center">
            {{ t('message.datatable.no_data') }}
        </div>
    </div>

    <Pagination :links="messages.links" :meta="messages.meta" :only="only"
                class="fixed left-0 lg:left-64 right-0 bottom-0" />

    <!-- Delete Messages Confirmation Modal -->
    <ConfirmationModal :show="deleteMessagesForm.ids.length > 0" @close="deleteMessagesForm.reset()">
        <template #title>
            {{ t('message.messages.delete', deleteMessagesForm.ids.length) }}
        </template>

        <template #content>
            {{ t('message.messages.delete_confirmation', deleteMessagesForm.ids.length) }}
        </template>

        <template #footer>
            <SecondaryButton @click="deleteMessagesForm.reset()">
                {{ t('action.cancel') }}
            </SecondaryButton>

            <DangerButton
                :class="{ 'opacity-25': deleteMessagesForm.processing }"
                :disabled="deleteMessagesForm.processing"
                class="ml-3"
                @click="deleteMessages"
            >
                {{ t('action.delete') }}
            </DangerButton>
        </template>
    </ConfirmationModal>

    <!-- Retry Messages Confirmation Modal -->
    <ConfirmationModal :show="retryMessagesForm.ids.length > 0" @close="retryMessagesForm.reset()">
        <template #title>
            {{ t('message.messages.retry', retryMessagesForm.ids.length) }}
        </template>

        <template #content>
            {{ t('message.messages.retry_confirmation', retryMessagesForm.ids.length) }}
        </template>

        <template #footer>
            <SecondaryButton @click="retryMessagesForm.reset()">
                {{ t('action.cancel') }}
            </SecondaryButton>

            <DangerButton
                :class="{ 'opacity-25': retryMessagesForm.processing }"
                :disabled="retryMessagesForm.processing"
                class="ml-3"
                @click="retryMessages"
            >
                {{ t('action.retry') }}
            </DangerButton>
        </template>
    </ConfirmationModal>
</template>
