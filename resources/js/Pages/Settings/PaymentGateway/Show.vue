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
import ActionMessage from '@/Components/ActionMessage.vue';
import CopyInput from '@/Components/CopyInput.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import Layout from '@/Pages/Settings/Layout.vue';
import { useForm } from '@inertiajs/vue3';
import { forEach } from 'lodash';
import { onBeforeMount } from 'vue';

const props = defineProps({
    paymentGateways: {
        type: Array,
        required: true,
    },
});

const forms = [];

const updatePaymentGatewayConfiguration = (paymentGateway) => {
    forms[paymentGateway].put(route('settings.payment-gateway.update', paymentGateway), {
        preserveScroll: true,
        preserveState: true,
    });
};

onBeforeMount(() => {
    forEach(props.paymentGateways, (paymentGateway) => {
        const form = useForm({
            enabled: paymentGateway.enabled,
            config: {},
        });
        forEach(paymentGateway.config, (value, field) => {
            form.config[field] = value;
        });
        form['webhook_url'] = route('payment-gateways.webhook', paymentGateway.name);
        form.defaults();
        forms[paymentGateway.name] = form;
    });
});
</script>

<template>
    <Layout title="Payment Gateway">
        <div>
            <div class="mx-auto py-10 sm:px-6 lg:px-8">
                <div v-for="(paymentGateway, index) in paymentGateways" :key="paymentGateway.name"
                     :class="{ 'mt-10 sm:mt-0': index > 0 }">
                    <FormSection :docs="paymentGateway.docs"
                                 @submitted="updatePaymentGatewayConfiguration(paymentGateway.name)">
                        <template #title>
                            {{ paymentGateway.label }}
                        </template>

                        <template #description>
                            {{ $t('message.settings.payment_gateway.description', { paymentGateway: paymentGateway.label }) }}
                        </template>

                        <template #form>
                            <div>
                                <SwitchInput
                                    :id="`${paymentGateway.name}.enabled`"
                                    v-model="forms[paymentGateway.name].enabled"
                                    :label="$t('field.enabled')"
                                    class="mt-1 block w-full" />
                            </div>
                            <template v-if="forms[paymentGateway.name].enabled">
                                <div v-for="(field, setting) in paymentGateway.fields" class="col-span-6">
                                    <InputLabel v-if="field.type !== 'boolean'" :for="setting" :value="field.label"
                                                required />
                                    <TextInput
                                        v-if="field.type === 'text'"
                                        :id="`${paymentGateway.name}.${setting}`"
                                        v-model="forms[paymentGateway.name].config[setting]"
                                        class="mt-1 block w-full"
                                        required
                                        type="text" />
                                    <TextAreaInput
                                        v-else-if="field.type === 'textarea'"
                                        :id="`${paymentGateway.name}.${setting}`"
                                        v-model="forms[paymentGateway.name].config[setting]"
                                        class="mt-1 block w-full"
                                        required
                                        type="text" />
                                    <SwitchInput
                                        v-else
                                        :id="`${paymentGateway.name}.${setting}`"
                                        v-model="forms[paymentGateway.name].config[setting]"
                                        :label="field.label"
                                        class="mt-1 block w-full" />
                                    <InputError :message="forms[paymentGateway.name].errors[setting]" class="mt-2" />
                                </div>
                                <div v-if="paymentGateway.name !== 'bank-transfer'" class="col-span-6">
                                    <InputLabel :value="$t('field.webhook_url')" for="webhook-url" />
                                    <CopyInput
                                        :id="`${paymentGateway.name}.webhook-url`"
                                        :content="forms[paymentGateway.name].webhook_url"
                                        class="mt-1 block w-full" />
                                </div>
                            </template>
                        </template>

                        <template #actions>
                            <ActionMessage :on="forms[paymentGateway.name].recentlySuccessful" class="mr-3">
                                {{ $t('message.saved') }}
                            </ActionMessage>

                            <PrimaryButton :class="{ 'opacity-25': forms[paymentGateway.name].processing }"
                                           :disabled="forms[paymentGateway.name].processing">
                                {{ $t('action.save') }}
                            </PrimaryButton>
                        </template>
                    </FormSection>

                    <SectionBorder v-if="paymentGateways.length !== index + 1" />
                </div>
            </div>
        </div>
    </Layout>
</template>
