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
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Card from '@/Pages/Install/Card.vue';
import { ChevronRightIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    params: Object,
});

const configForm = useForm({
    host: props.params.host,
    port: props.params.port,
    name: props.params.name,
    username: props.params.username,
    password: props.params.password,
});

const createConfig = () => {
    configForm.post(route('install.config.store'), {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Card>
        <template #header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.installation.database') }}</h2>
        </template>

        <template #content>
            <form id="config" @submit.prevent="createConfig">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel :value="$t('field.host')" for="host" required />
                            <TextInput id="host" v-model="configForm.host" autofocus class="mt-1 block w-full"
                                       required />
                            <InputError :message="configForm.errors.host" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="$t('field.port')" for="port" required />
                            <TextInput id="port" v-model="configForm.port" autofocus class="mt-1 block w-full"
                                       max="65535" min="1"
                                       required type="number" />
                            <InputError :message="configForm.errors.port" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <InputLabel :value="$t('field.name')" for="name" required />
                        <TextInput id="name" v-model="configForm.name" autofocus class="mt-1 block w-full" required />
                        <InputError :message="configForm.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :value="$t('field.username')" for="username" required />
                        <TextInput id="username" v-model="configForm.username" autofocus class="mt-1 block w-full"
                                   required />
                        <InputError :message="configForm.errors.username" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel :value="$t('field.password')" for="password" />
                        <TextInput id="password" v-model="configForm.password" autofocus class="mt-1 block w-full"
                                   type="password" />
                        <InputError :message="configForm.errors.password" class="mt-2" />
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <PrimaryButton class="w-full justify-center" form="config">
                {{ $t('action.next') }}
                <ChevronRightIcon aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" />
            </PrimaryButton>
        </template>
    </Card>
</template>
