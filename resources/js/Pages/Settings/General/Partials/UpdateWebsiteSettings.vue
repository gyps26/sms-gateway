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
import FileInput from "@/Components/FileInput.vue";
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from "@/Components/SwitchInput.vue";
import TextInput from '@/Components/TextInput.vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

const form = useForm({
    _method: 'PUT',
    name: page.props.app.name,
    app: null,
    logo: null,
    homepage: page.props.settings.homepage,
    support_url: page.props.settings.support_url,
});

const logoPreview = ref(null);
const logoInput = ref(null);

const updateSiteInformation = () => {
    if (logoInput.value) {
        form.logo = logoInput.value.files[0];
    }

    form.post(route('settings.general.update'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => clearLogoFileInput(),
    });
};

const selectNewPhoto = () => {
    logoInput.value.click();
};

const updateLogoPreview = () => {
    const photo = logoInput.value.files[0];

    if (! photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        logoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route('logo.destroy'), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            logoPreview.value = null;
            clearLogoFileInput();
        },
    });
};

const clearLogoFileInput = () => {
    if (logoInput.value?.value) {
        logoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection docs="https://rbsoft.org/docs/sms-gateway/enhanced/general-settings.html#website"
                 @submitted="updateSiteInformation">
        <template #title>
            {{ $t('message.settings.website.title') }}
        </template>

        <template #description>
            {{ $t('message.settings.website.description') }}
        </template>

        <template #form>
            <!-- Logo -->
            <div class="col-span-6">
                <!-- Logo File Input -->
                <input
                    ref="logoInput"
                    class="hidden"
                    type="file"
                    @change="updateLogoPreview"
                >

                <InputLabel :value="$t('field.logo')" for="logo" />

                <!-- Current Site Logo -->
                <div v-show="! logoPreview" class="mt-2">
                    <img :alt="page.props.app.name" :src="page.props.app.logo"
                         class="rounded-full h-20 w-20 object-cover">
                </div>

                <!-- New Site Logo Preview -->
                <div v-show="logoPreview" class="mt-2">
                    <span
                        :style="'background-image: url(\'' + logoPreview + '\');'"
                        class="block rounded-full w-20 h-20 bg-cover bg-no-repeat bg-center"
                    />
                </div>

                <SecondaryButton class="mt-2 mr-2" type="button" @click.prevent="selectNewPhoto">
                    {{ $t('action.new_logo') }}
                </SecondaryButton>

                <SecondaryButton
                    v-if="page.props.logoUrl"
                    class="mt-2"
                    type="button"
                    @click.prevent="deletePhoto"
                >
                    {{ $t('action.remove_logo') }}
                </SecondaryButton>

                <InputError :message="form.errors.logo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6">
                <InputLabel :value="$t('field.name')" for="name" required />
                <TextInput
                    id="name"
                    v-model="form.name"
                    autocomplete="name"
                    class="mt-1 block w-full"
                    required
                    type="text"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Support URL -->
            <div class="col-span-6">
                <InputLabel :value="$t('field.support_url')" for="support-url" required />
                <TextInput
                    id="support-url"
                    v-model="form.support_url"
                    class="mt-1 block w-full"
                    required
                    type="text"
                />
                <InputError :message="form.errors.support_url" class="mt-2" />
            </div>

            <!-- Android Application -->
            <div class="col-span-6">
                <InputLabel :value="$t('field.app')" for="app" />
                <FileInput id="app" v-model="form.app"
                           accept=".apk"
                           class="mt-1 block w-full" />
                <InputError :message="form.errors.app" class="mt-2" />
            </div>

            <div class="col-span-6">
                <SwitchInput v-model="form.homepage" :label="$t('field.homepage')" />
                <InputError :message="form.errors.homepage" class="mt-2" />
            </div>
        </template>

        <template #actions>
            <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                {{ $t('message.saved') }}
            </ActionMessage>

            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                {{ $t('action.save') }}
            </PrimaryButton>
        </template>
    </FormSection>
</template>
