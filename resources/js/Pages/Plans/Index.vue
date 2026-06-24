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
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import ToggleableInput from '@/Components/ToggleableInput.vue';
import { useEnums } from '@/Composables/useEnums.js';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PencilSquareIcon, PlusIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { cloneDeep } from 'lodash';
import { computed, ref } from 'vue';

const props = defineProps({
    plans: {
        type: Object,
        required: true,
    },
    currencies: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingPlan = ref(false);
const planBeingUpdated = ref(null);

const managingPlan = computed({
    get() {
        return creatingPlan.value || planBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                managePlanForm.name = val.name;
                managePlanForm.description = val.description;
                managePlanForm.interval = val.interval;
                managePlanForm.interval_unit = val.interval_unit;
                managePlanForm.price = val.price;
                managePlanForm.currency = val.currency;
                managePlanForm.position = val.position;
                managePlanForm.features = cloneDeep(val.features);
                managePlanForm.enabled = val.enabled;
                planBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingPlan.value = val;
        } else {
            creatingPlan.value = false;
            planBeingUpdated.value = null;
            managePlanForm.reset();
            managePlanForm.clearErrors();
        }
    },
});

const managePlanForm = useForm({
    name: null,
    description: null,
    interval: 1,
    interval_unit: 'Month',
    price: null,
    currency: 'USD',
    position: null,
    features: {
        credits: null,
        contact_lists: null,
        contacts: null,
        devices: null,
        sending_servers: null,
        sender_ids: null,
        templates: null,
        webhooks: null,
        api_tokens: null,
        auto_responses: null,
        data_export: false,
    },
    enabled: true,
});

const managePlan = () => {
    if (planBeingUpdated.value !== null) {
        managePlanForm.put(route('plans.update', planBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingPlan.value = false,
        });
    } else {
        managePlanForm.post(route('plans.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingPlan.value = false,
        });
    }
};

const intervalUnits = useEnums().intervalUnit;

const columns = [
    { name: t('field.name'), field: 'name' },
    { name: t('field.description'), field: 'description', sortable: false },
    {
        name: t('field.interval'),
        field: 'interval',
        render: (interval, plan) => `${ interval } ${ intervalUnits[plan.interval_unit] }`,
        sortable: false,
    },
    { name: t('field.price'), field: 'price' },
    { name: t('field.currency'), field: 'currency' },
    { name: t('field.position'), field: 'position' },
    { name: t('field.enabled'), field: 'enabled' },
    { name: t('field.credits'), field: 'features.credits', render: (credits) => credits ?? '∞', sortable: false },
    {
        name: t('field.contact_lists'),
        field: 'features.contact_lists',
        render: (contactLists) => contactLists ?? '∞',
        sortable: false
    },
    { name: t('field.contacts'), field: 'features.contacts', render: (contacts) => contacts ?? '∞', sortable: false },
    { name: t('field.devices'), field: 'features.devices', render: (devices) => devices ?? '∞', sortable: false },
    {
        name: t('field.sending_servers'),
        field: 'features.sending_servers',
        render: (sendingServers) => sendingServers ?? '∞',
        sortable: false
    },
    {
        name: t('field.sender_ids'),
        field: 'features.sender_ids',
        render: (senderIds) => senderIds ?? '∞',
        sortable: false
    },
    { name: t('field.webhooks'), field: 'features.webhooks', render: (webhooks) => webhooks ?? '∞', sortable: false },
    {
        name: t('field.api_tokens'),
        field: 'features.api_tokens',
        render: (apiTokens) => apiTokens ?? '∞',
        sortable: false
    },
    {
        name: t('field.auto_responses'),
        field: 'features.auto_responses',
        render: (autoResponses) => autoResponses ?? '∞',
        sortable: false
    },
    { name: t('field.data_export'), field: 'features.data_export', sortable: false },
    { name: t('field.created_at'), field: 'created_at', render: (createdAt) => (new Date(createdAt)).toLocaleString() },
    { name: t('field.updated_at'), field: 'updated_at', render: (updatedAt) => (new Date(updatedAt)).toLocaleString() },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (plan) => managingPlan.value = plan,
        screenReader: (plan) => t('message.plans.action.edit', { name: plan.name }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.plans')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.plans') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingPlan = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="plans" :columns="columns" :only="['plans']" :params="params" />

        <!-- Manage Plan Modal -->
        <DialogModal :show="managingPlan" @close="managingPlan = false">
            <template #title>
                {{ planBeingUpdated ? t('message.plans.edit') : t('message.plans.add') }}
            </template>

            <template #content>
                <form id="manage-plan" @submit.prevent="managePlan">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" required />
                            <TextInput id="name" v-model="managePlanForm.name" autofocus class="mt-1 block w-full"
                                       required type="text" />
                            <InputError :message="managePlanForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.description')" for="description" required />
                            <TextAreaInput id="description" v-model="managePlanForm.description"
                                           class="mt-1 block w-full" required />
                            <InputError :message="managePlanForm.errors.description" class="mt-2" />
                        </div>
                        <template v-if="creatingPlan">
                            <div>
                                <InputLabel :value="t('field.price')" for="price" required />
                                <TextInput id="price" v-model="managePlanForm.price" class="mt-1 block w-full"
                                           min="0" required step="any" type="number" />
                                <InputError :message="managePlanForm.errors.price" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel :value="t('field.currency')" for="currency" required />
                                <ComboboxInput id="currency" v-model="managePlanForm.currency"
                                               :options="currencies" class="mt-1 block w-full" />
                                <InputError :message="managePlanForm.errors.currency" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel :value="t('field.interval')" for="interval" required />
                                <TextInput id="interval" v-model="managePlanForm.interval" class="mt-1 block w-full"
                                           min="1" required type="number" />
                                <InputError :message="managePlanForm.errors.interval" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel :value="t('field.interval_unit')" for="inteval_unit" required />
                                <ComboboxInput id="interval_unit" v-model="managePlanForm.interval_unit"
                                               :options="intervalUnits" class="mt-1 block w-full" />
                                <InputError :message="managePlanForm.errors.interval_unit" class="mt-2" />
                            </div>
                        </template>
                        <div>
                            <InputLabel :value="t('field.position')" for="position" required />
                            <TextInput id="position" v-model="managePlanForm.position" class="mt-1 block w-full"
                                       min="1" required type="number" />
                            <InputError :message="managePlanForm.errors.position" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.credits')" for="credits" />
                            <ToggleableInput id="credits" v-model="managePlanForm.features.credits"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.credits']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.contact_lists')" for="contact_lists" />
                            <ToggleableInput id="contact_lists" v-model="managePlanForm.features.contact_lists"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.contact_lists']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.contacts')" for="contacts" />
                            <ToggleableInput id="contacts" v-model="managePlanForm.features.contacts"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.contacts']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.devices')" for="devices" />
                            <ToggleableInput id="devices" v-model="managePlanForm.features.devices"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.devices']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.sending_servers')" for="sending-servers" />
                            <ToggleableInput id="sending-servers" v-model="managePlanForm.features.sending_servers"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.sending_servers']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.sender_ids')" for="sender-ids" />
                            <ToggleableInput id="sender-ids" v-model="managePlanForm.features.sender_ids"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.sender_ids']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.templates')" for="templates" />
                            <ToggleableInput id="templates" v-model="managePlanForm.features.templates"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.templates']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.webhooks')" for="webhooks" />
                            <ToggleableInput id="webhooks" v-model="managePlanForm.features.webhooks"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.webhooks']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.api_tokens')" for="api-tokens" />
                            <ToggleableInput id="api-tokens" v-model="managePlanForm.features.api_tokens"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.api_tokens']" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.auto_responses')" for="auto-responses" />
                            <ToggleableInput id="auto-responses" v-model="managePlanForm.features.auto_responses"
                                             class="mt-1 block w-full" min="0"
                                             type="number" />
                            <InputError :message="managePlanForm.errors['features.auto_responses']" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput id="data_export" v-model="managePlanForm.features.data_export"
                                         :label="t('field.data_export')"
                                         class="mt-1 block w-full" />
                            <InputError :message="managePlanForm.errors['features.data_export']" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput id="enabled" v-model="managePlanForm.enabled" :label="t('field.enabled')"
                                         class="mt-1 block w-full" />
                            <InputError :message="managePlanForm.errors.enabled" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingPlan = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': managePlanForm.processing }"
                    :disabled="managePlanForm.processing"
                    class="ml-3"
                    form="manage-plan"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
