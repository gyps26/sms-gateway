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
import { useI18n } from '@/Composables/useI18n.js';
import { computed } from 'vue';

const props = defineProps({
    sims: {
        type: Array,
        default: [],
    },
    senderIds: {
        type: Array,
        default: [],
    },
});

const { t } = useI18n();

const senderId = defineModel('senderId');
const sim = defineModel('sim');

const messageable = computed({
    get: () => {
        if (sim.value) {
            return `sim:${ sim.value }`;
        }

        if (senderId.value) {
            return `sender_id:${ senderId.value }`;
        }

        return null;
    },
    set: (value) => {
        sim.value = null;
        senderId.value = null;

        if (value) {
            const [type, id] = value.split(':');
            if (type === 'sim') {
                sim.value = Number(id);
            } else if (type === 'sender_id') {
                senderId.value = Number(id);
            }
        }
    }
})

const messageableOptions = computed(() => {
    if (props.sims.length > 0 || props.senderIds.length > 0) {
        let items = [
            {
                type: 'group',
                label: t('entity.sim'),
                key: 'sim',
                children: [],
            },
            {
                type: 'group',
                label: t('entity.sender_id'),
                key: 'sender_id',
                children: [],
            },
        ];

        items[0].children = props.sims.map(function (sim) {
            return { value: `sim:${ sim.id }`, label: sim.label };
        });
        items[1].children = props.senderIds.map(function (senderId) {
            return { value: `sender_id:${ senderId.id }`, label: senderId.value };
        });

        return items;
    }
    return [];
});
</script>

<template>
    <ComboboxInput id="messageable" v-model="messageable"
                   :options="messageableOptions" />
</template>
