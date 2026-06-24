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
import { ChevronRightIcon } from '@heroicons/vue/20/solid/index.js';
import { useForm } from '@inertiajs/vue3';

const adminForm = useForm({
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    license_code: null,
});

const createAdmin = () => {
    adminForm.post(route('install.admin.store'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <Card>
        <template #header>
            <h2 class="text-lg font-medium text-gray-900">{{ $t('message.installation.admin') }}</h2>
        </template>

        <template #content>
            <form id="create-admin" @submit.prevent="createAdmin">
                <div class="space-y-4">
                    <div>
                        <InputLabel :value="$t('field.name')" for="name" required />
                        <TextInput id="name" v-model="adminForm.name" autofocus class="mt-1 block w-full" required />
                        <InputError :message="adminForm.errors.name" class="mt-2" />
                    </div>
                    <div>

                        <InputLabel :value="$t('field.email')" for="email" required />
                        <TextInput id="email" v-model="adminForm.email" autofocus class="mt-1 block w-full" required
                                   type="email" />
                        <InputError :message="adminForm.errors.email" class="mt-2" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel :value="$t('field.password')" for="password" required />
                            <TextInput id="password" v-model="adminForm.password" autofocus class="mt-1 block w-full"
                                       required type="password" />
                            <InputError :message="adminForm.errors.password" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="$t('field.confirm_password')" for="confirm-password" required />
                            <TextInput id="confirm-password" v-model="adminForm.password_confirmation" autofocus
                                       class="mt-1 block w-full"
                                       required
                                       type="password" />
                            <InputError :message="adminForm.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>
                    <div>
                        <InputLabel :value="$t('field.license_code')" for="license-code" required />
                        <TextInput id="license-code" v-model="adminForm.license_code" class="mt-1 block w-full" required />
                        <InputError :message="adminForm.errors.license_code" class="mt-2" />
                    </div>
                </div>
            </form>
        </template>

        <template #footer>
            <PrimaryButton class="w-full justify-center" form="create-admin">
                {{ $t('action.next') }}
                <ChevronRightIcon aria-hidden="true" class="w-5 h-5 ml-2 -mr-1" />
            </PrimaryButton>
        </template>
    </Card>
</template>
