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
import Cell from "@/Components/Cell.vue";
import Checkbox from '@/Components/Checkbox.vue';
import ComboboxInput from '@/Components/ComboboxInput.vue';
import DropDown from '@/Components/DropDown.vue';
import Pagination from '@/Components/Pagination.vue';
import SelectionMenu from '@/Components/SelectionMenu.vue';
import TextInput from '@/Components/TextInput.vue';
import { useQueryFilter } from '@/Composables/useQueryFilter.js';
import { ChevronDownIcon, ChevronUpIcon, MagnifyingGlassIcon, MinusIcon } from '@heroicons/vue/20/solid';
import { debounce, get } from 'lodash';
import { computed, reactive, ref, watch } from 'vue';

const props = defineProps({
    primaryKey: {
        type: String,
        default: 'id',
    },
    actions: {
        type: Array,
        default: [],
    },
    columns: {
        type: Array,
        required: true,
    },
    collection: {
        type: Object,
        required: true,
    },
    params: {
        type: Object,
        required: true,
    },
    bulkActions: {
        type: Array,
        default: [],
    },
    only: {
        type: Array,
        default: []
    },
    defaults: {
        type: Object,
        default: {},
    },
});

const perPageOptions = [10, 25, 50, 100];

const selected = ref([]);
const selectAll = ref(false);

const defaults = {
    per_page: perPageOptions[0],
    search: '',
    sort_by: 'created_at',
    sort_direction: 'desc',
    ...props.defaults
};

const queryParams = reactive({
    per_page: props.params.per_page ?? defaults.per_page,
    search: props.params.search ?? defaults.search,
    sort_by: props.params.sort_by ?? defaults.sort_by,
    sort_direction: props.params.sort_direction ?? defaults.sort_direction,
});

const visibleColumns = computed(() => {
    return props.columns.filter((column) => column.visible === undefined || column.visible);
});
const primaryKeys = computed(() => {
    return props.collection.data.map((o) => o[props.primaryKey]);
});
const allowSelection = computed(() => {
    return props.bulkActions.length > 0;
});

const sortBy = (field) => {
    queryParams.sort_direction = queryParams.sort_by !== field
        ? 'asc'
        : queryParams.sort_direction === 'asc' ? 'desc' : 'asc';
    queryParams.sort_by = field;
};

const columnValue = (row, column) => {
    const val = get(row, column.field);
    return column.render ? column.render(val, row) : val;
};

const refresh = () => useQueryFilter(queryParams, defaults).refresh(props.only);

watch(() => [queryParams.per_page, queryParams.sort_by, queryParams.sort_direction], refresh);
watch(() => queryParams.search, debounce(refresh, 300));
</script>

<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <div class="flex space-x-2">
                    <DropDown v-if="selected.length > 0" :items="bulkActions"
                              @select="(item) => item.callback({
                                  'ids': selected,
                                  'all': selectAll,
                                  'search': queryParams.search
                              })">
                        <span class="bg-gray-500 text-white px-1 mr-1 rounded-sm">
                            {{ selectAll ? collection.meta.total : selected.length }}
                        </span>
                        {{ $t('message.datatable.actions') }}
                    </DropDown>
                    <ComboboxInput v-model="queryParams.per_page" :clearable="false"
                                   :hideSearchBox="true" :options="perPageOptions"
                                   class="w-24" />
                </div>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none space-x-2">
                <div class="relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <MagnifyingGlassIcon aria-hidden="true" class="h-5 w-5 text-gray-400" />
                    </div>
                    <TextInput v-model="queryParams.search" class="block w-full pl-10" type="text" />
                </div>
            </div>
        </div>
        <div class="mt-8 flex flex-col">
            <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                    <div class="relative overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                        <table class="min-w-full table-fixed divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr class="divide-x divide-gray-200">
                                <SelectionMenu v-if="allowSelection" v-model:select-all="selectAll"
                                               v-model:selected="selected" :ids="primaryKeys" as="th"
                                               class="w-16" />
                                <th v-for="(column, index) in visibleColumns" :key="column.name"
                                    :class="[
                                        index === 0
                                            ? 'pl-4 pr-4 sm:pl-6'
                                            : (index === visibleColumns.length - 1 && actions.length === 0 ? 'pl-4 pr-4 sm:pr-6' : 'px-4'),
                                        'py-3.5 text-left text-sm font-semibold text-gray-900'
                                    ]"
                                    scope="col">
                                    <template v-if="column.sortable === undefined || column.sortable">
                                        <a class="group inline-flex" href="#" @click.prevent="sortBy(column.field)">
                                            {{ column.name }}
                                            <span
                                                :class="[
                                                    queryParams.sort_by === column.field
                                                            ? 'bg-gray-200 text-gray-900 group-hover:bg-gray-300'
                                                            : 'invisible text-gray-400 group-hover:visible group-focus:visible',
                                                    'ml-2 flex-none rounded h-full'
                                                ]">
                                                <ChevronDownIcon
                                                    v-if="queryParams.sort_by === column.field && queryParams.sort_direction === 'desc'"
                                                    aria-hidden="true" class="h-5 w-5" />
                                                <ChevronUpIcon v-else aria-hidden="true" class="h-5 w-5" />
                                            </span>
                                        </a>
                                    </template>
                                    <template v-else>
                                        {{ column.name }}
                                    </template>
                                </th>
                                <th v-if="actions.length > 0 && props.collection.data.length > 0"
                                    class="relative py-4 pl-4 pr-4 sm:pr-6">
                                    <span class="sr-only">{{ $t('message.datatable.actions') }}</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="row in collection.data" v-if="props.collection.data.length > 0"
                                :key="row[primaryKey]"
                                :class="[{ 'bg-gray-50': allowSelection && selected.includes(row[primaryKey]) }, 'divide-x divide-gray-200']">
                                <td v-if="allowSelection" class="relative w-12 px-6 sm:w-16 sm:px-8">
                                    <div v-if="selected.includes(row[primaryKey])"
                                         class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"></div>
                                    <Checkbox v-model:checked="selected" :value="row[primaryKey]"
                                              class="absolute left-4 top-1/2 -mt-2 h-4 w-4 sm:left-6" />
                                </td>
                                <td v-for="(column, index) in visibleColumns" :key="column.name"
                                    :class="[
                                        'text-sm',
                                        index === 0
                                            ? ['py-4 pl-4 pr-4 sm:pl-6 font-medium', selected.includes(row[primaryKey]) ? 'text-indigo-600' : 'text-gray-900']
                                            : ['text-gray-500', index === visibleColumns.length - 1 && actions.length === 0 ? 'py-4 pl-4 pr-4 sm:pr-6' : 'p-4']
                                    ]">
                                    <slot :name="column.field" :row="row">
                                        <Cell :value="columnValue(row, column)" />
                                    </slot>
                                </td>
                                <td v-if="actions.length > 0 && props.collection.data.length > 0"
                                    class="whitespace-nowrap py-4 pl-4 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <span class="flex flex-row justify-end space-x-2">
                                        <template v-if="visibleActions = actions.filter((i) => i.visible === undefined || i.visible(row))">
                                            <template v-if="visibleActions.length > 0">
                                                <a v-for="action in visibleActions"
                                                   :key="action.name"
                                                   :title="action.name"
                                                   class="text-indigo-600 hover:text-indigo-900"
                                                   href="#"
                                                   @click.prevent="action.callback(row)">
                                                    <component :is="action.icon" v-if="action.icon" aria-hidden="true"
                                                               class="-ml-0.5 mr-2 h-5 w-5" />
                                                    <template v-else>{{ action.name }}</template>
                                                    <span v-if="action.screenReader" class="sr-only">
                                                        {{ action.screenReader(row) }}
                                                    </span>
                                                </a>
                                            </template>
                                            <template v-else>
                                                <MinusIcon aria-hidden="true" class="-ml-0.5 mr-2 size-5 text-gray-500" />
                                            </template>
                                        </template>
                                    </span>
                                </td>
                            </tr>
                            <tr v-else>
                                <td :colspan="props.columns.length + (props.actions.length > 0 ? 2 : 1)"
                                    class="whitespace-nowrap px-3 py-4 text-gray-500">
                                    <div class="text-2xl flex justify-center items-center h-44">
                                        {{ $t('message.datatable.no_data') }}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <Pagination :links="collection.links" :meta="collection.meta" :only="only" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
