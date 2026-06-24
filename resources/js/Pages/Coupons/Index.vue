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
import DataTable from '@/Components/DataTable.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import SwitchInput from '@/Components/SwitchInput.vue';
import TextInput from '@/Components/TextInput.vue';
import ToggleableInput from "@/Components/ToggleableInput.vue";
import { useDateTime } from '@/Composables/useDateTime';
import { useI18n } from '@/Composables/useI18n.js';
import ContentLayout from '@/Layouts/ContentLayout.vue';
import { PencilSquareIcon, PlusIcon } from '@heroicons/vue/20/solid';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    coupons: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
});

const { t } = useI18n();

const creatingCoupon = ref(false);
const couponBeingUpdated = ref(null);

const managingCoupon = computed({
    get() {
        return creatingCoupon.value || couponBeingUpdated.value !== null;
    },

    set(val) {
        if (val) {
            if (typeof val === 'object') {
                manageCouponForm.name = val.name;
                manageCouponForm.code = val.code;
                manageCouponForm.discount = val.discount;
                manageCouponForm.quantity = val.quantity;
                manageCouponForm.expires_at = useDateTime().getISOString(val.expires_at);
                manageCouponForm.enabled = val.enabled;
                couponBeingUpdated.value = val;
            } else if (typeof val === 'boolean') creatingCoupon.value = val;
        } else {
            creatingCoupon.value = false;
            couponBeingUpdated.value = null;
            manageCouponForm.reset();
            manageCouponForm.clearErrors();
        }
    },
});

const manageCouponForm = useForm({
    name: null,
    code: null,
    discount: null,
    quantity: null,
    expires_at: null,
    enabled: true,
});

const manageCoupon = () => {
    const form = manageCouponForm.transform((data) => ({
        ...data,
        expires_at: data.expires_at ? new Date(data.expires_at).toISOString() : null,
    }));

    if (couponBeingUpdated.value !== null) {
        form.put(route('coupons.update', couponBeingUpdated.value), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingCoupon.value = false,
        });
    } else {
        form.post(route('coupons.store'), {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => managingCoupon.value = false,
        });
    }
};

const columns = [
    { name: t('field.name'), field: 'name' },
    { name: t('field.code'), field: 'code' },
    { name: t('field.discount'), field: 'discount' },
    { name: t('field.quantity'), field: 'quantity', render: (quantity) => quantity === null ? '∞' : quantity },
    { name: t('field.redeemed'), field: 'redeemed' },
    { name: t('field.enabled'), field: 'enabled' },
    {
        name: t('field.expires_at'),
        field: 'expires_at',
        render: (data) => data ? new Date(data).toLocaleString() : 'Never',
    },
    { name: t('field.updated_at'), field: 'updated_at', render: (data) => new Date(data).toLocaleString() },
];

const actions = [
    {
        name: t('action.edit'),
        icon: PencilSquareIcon,
        callback: (coupon) => managingCoupon.value = coupon,
        screenReader: (coupon) => t('message.coupons.action.edit', { coupon: coupon.name }),
    },
];
</script>

<template>
    <ContentLayout :title="t('page.coupons')">
        <template #header>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ t('page.coupons') }}
                </h1>
            </div>
            <div class="mt-6 flex space-x-3 md:mt-0 md:ml-4">
                <PrimaryButton type="button" @click="creatingCoupon = true">
                    <PlusIcon aria-hidden="true" class="-ml-0.5 mr-2 h-4 w-4" />
                    {{ t('action.add') }}
                </PrimaryButton>
            </div>
        </template>

        <DataTable :actions="actions" :collection="coupons" :columns="columns" :params="params" />

        <!-- Manage Coupon Modal -->
        <DialogModal :show="managingCoupon" @close="managingCoupon = false">
            <template #title>
                {{ couponBeingUpdated ? t('message.coupons.edit') : t('message.coupons.add') }}
            </template>

            <template #content>
                <form id="manage-coupon" @submit.prevent="manageCoupon">
                    <div class="space-y-4">
                        <div>
                            <InputLabel :value="t('field.name')" for="name" required />
                            <TextInput id="name" v-model="manageCouponForm.name" autofocus class="mt-1 block w-full"
                                       required />
                            <InputError :message="manageCouponForm.errors.name" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.code')" for="code" required />
                            <TextInput id="code" v-model="manageCouponForm.code" class="mt-1 block w-full" required />
                            <InputError :message="manageCouponForm.errors.code" class="mt-2" />
                        </div>
                        <template v-if="creatingCoupon">
                            <div>
                                <InputLabel :value="t('field.discount')" for="discount" required />
                                <TextInput id="discount" v-model="manageCouponForm.discount" class="mt-1 block w-full"
                                           required />
                                <InputError :message="manageCouponForm.errors.discount" class="mt-2" />
                            </div>
                        </template>
                        <div>
                            <InputLabel :value="t('field.quantity')" for="quantity" required />
                            <ToggleableInput id="quantity" v-model="manageCouponForm.quantity" class="mt-1 block w-full"
                                             min="0"
                                             required
                                             type="number" />
                            <InputError :message="manageCouponForm.errors.quantity" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel :value="t('field.expires_at')" for="expires-at" />
                            <TextInput id="expires-at" v-model="manageCouponForm.expires_at" class="mt-1 block w-full"
                                       type="datetime-local" />
                            <InputError :message="manageCouponForm.errors.expires_at" class="mt-2" />
                        </div>
                        <div>
                            <SwitchInput id="enabled" v-model="manageCouponForm.enabled" :label="t('field.enabled')"
                                         class="mt-1 block w-full" />
                            <InputError :message="manageCouponForm.errors.enabled" class="mt-2" />
                        </div>
                    </div>
                </form>
            </template>

            <template #footer>
                <SecondaryButton @click="managingCoupon = false">
                    {{ t('action.cancel') }}
                </SecondaryButton>

                <PrimaryButton
                    :class="{ 'opacity-25': manageCouponForm.processing }"
                    :disabled="manageCouponForm.processing"
                    class="ml-3"
                    form="manage-coupon"
                >
                    {{ t('action.save') }}
                </PrimaryButton>
            </template>
        </DialogModal>
    </ContentLayout>
</template>
