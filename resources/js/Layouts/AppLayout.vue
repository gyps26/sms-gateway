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
import DropDownTransition from '@/Components/DropDownTransition.vue';
import LanguagePicker from '@/Components/LanguagePicker.vue';
import NavLink from '@/Components/NavLink.vue';
import { useI18n } from '@/Composables/useI18n.js';
import {
    Dialog,
    DialogPanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import { ArrowDownTrayIcon, ChevronDownIcon } from '@heroicons/vue/20/solid';
import {
    ArrowLeftEndOnRectangleIcon,
    ArrowUpOnSquareIcon,
    ArrowUturnLeftIcon,
    BanknotesIcon,
    Bars3CenterLeftIcon,
    ChatBubbleLeftRightIcon,
    ClipboardDocumentListIcon,
    CodeBracketIcon,
    Cog6ToothIcon,
    Cog8ToothIcon,
    CpuChipIcon,
    CreditCardIcon,
    CubeTransparentIcon,
    DevicePhoneMobileIcon,
    DocumentTextIcon,
    EnvelopeIcon,
    HomeIcon,
    IdentificationIcon,
    ListBulletIcon,
    MegaphoneIcon,
    QuestionMarkCircleIcon,
    PaperAirplaneIcon,
    PhoneArrowUpRightIcon,
    PhoneIcon,
    ServerStackIcon,
    ShoppingCartIcon,
    Squares2X2Icon,
    TagIcon,
    TicketIcon,
    UserGroupIcon,
    UsersIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const { t } = useI18n();
const page = usePage();

const navigation = ref([
    { name: t('page.dashboard'), route: 'dashboard', icon: HomeIcon },
    { name: t('page.devices'), route: 'devices.index', icon: DevicePhoneMobileIcon },
    { name: t('page.sims'), route: 'sims.index', icon: CpuChipIcon },
    { name: t('page.contact_lists'), route: 'contact-lists.index', icon: UserGroupIcon },
    { name: t('page.blacklist'), route: 'blacklist.index', icon: ListBulletIcon },
    { name: t('page.templates'), route: 'templates.index', icon: Squares2X2Icon },
    { name: t('page.campaigns'), route: 'campaigns.index', icon: MegaphoneIcon },
    { name: t('page.send'), route: 'messages.create', icon: PaperAirplaneIcon },
    { name: t('page.messages'), route: 'messages.index', icon: EnvelopeIcon },
    { name: t('page.chat'), route: 'chat', icon: ChatBubbleLeftRightIcon },
    { name: t('page.call_log'), route: 'calls.index', icon: PhoneIcon },
    { name: t('page.auto_responder'), route: 'auto-responses.index', icon: ArrowUturnLeftIcon },
    { name: t('page.ussd_pulls'), route: 'ussd-pulls.index', icon: PhoneArrowUpRightIcon },
    { name: t('page.sending_servers'), route: 'sending-servers.index', icon: ServerStackIcon },
    { name: t('page.sender_ids'), route: 'sender-ids.index', icon: TagIcon },
    { name: t('page.webhooks'), route: 'webhooks.index', icon: CubeTransparentIcon },
    { name: t('page.subscriptions'), route: 'subscriptions.index', icon: CreditCardIcon },
    { name: t('page.payments'), route: 'payments.index', icon: BanknotesIcon },
    { name: t('page.subscribe'), route: 'subscribe', icon: ShoppingCartIcon, visible: () => ! page.props.auth.user.is_admin && page.props.plansCount > 0 }
]);

const secondaryNavigation = [
    { name: t('page.users'), route: 'users.index', icon: UsersIcon },
    { name: t('page.settings'), route: 'settings.general.edit', icon: Cog8ToothIcon },
    { name: t('page.plans'), route: 'plans.index', icon: ClipboardDocumentListIcon },
    { name: t('page.taxes'), route: 'taxes.index', icon: DocumentTextIcon },
    { name: t('page.coupons'), route: 'coupons.index', icon: TicketIcon },
];

const logout = () => window.Android ? window.Android.logout() : router.post(route('logout'));

const sidebarOpen = ref(false);
</script>

<template>
    <div class="min-h-full">
        <TransitionRoot :show="sidebarOpen" as="template">
            <Dialog as="div" class="relative z-40 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300"
                                 enter-from="opacity-0" enter-to="opacity-100"
                                 leave="transition-opacity ease-linear duration-300" leave-from="opacity-100"
                                 leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
                </TransitionChild>

                <div class="fixed inset-0 flex z-40">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform"
                                     enter-from="-translate-x-full" enter-to="translate-x-0"
                                     leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0"
                                     leave-to="-translate-x-full">
                        <DialogPanel class="relative flex-1 flex flex-col max-w-xs w-full pt-5 pb-4 bg-cyan-700">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
                                             enter-to="opacity-100" leave="ease-in-out duration-300"
                                             leave-from="opacity-100" leave-to="opacity-0">
                                <div class="absolute top-0 right-0 -mr-12 pt-2">
                                    <button
                                        class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                        type="button"
                                        @click="sidebarOpen = false">
                                        <span class="sr-only">{{ t('message.close_sidebar') }}</span>
                                        <XMarkIcon aria-hidden="true" class="h-6 w-6 text-white" />
                                    </button>
                                </div>
                            </TransitionChild>
                            <div class="flex-shrink-0 flex items-center px-4 space-x-2">
                                <img :alt="page.props.app.name"
                                     :src="page.props.app.logo"
                                     class="h-8 w-auto" />
                            </div>
                            <nav aria-label="Sidebar"
                                 class="mt-5 flex-shrink-0 h-full divide-y divide-cyan-800 overflow-y-auto">
                                <div class="px-2 space-y-1">
                                    <NavLink v-for="item in navigation.filter(el => el.visible === undefined || el.visible())" :key="item.name" :route-name="item.route"
                                             responsive
                                             @click="sidebarOpen = false">
                                        <component :is="item.icon" aria-hidden="true"
                                                   class="mr-4 flex-shrink-0 h-6 w-6 text-cyan-200" />
                                        {{ item.name }}
                                    </NavLink>
                                </div>
                                <div v-if="page.props.auth.user.is_admin" class="mt-6 pt-6">
                                    <div class="px-2 space-y-1">
                                        <NavLink v-for="item in secondaryNavigation"
                                                 :key="item.name"
                                                 :route-name="item.route"
                                                 responsive
                                                 @click="sidebarOpen = false">
                                            <component :is="item.icon" aria-hidden="true"
                                                       class="mr-4 flex-shrink-0 h-6 w-6 text-cyan-200" />
                                            {{ item.name }}
                                        </NavLink>
                                    </div>
                                </div>
                            </nav>
                        </DialogPanel>
                    </TransitionChild>
                    <div aria-hidden="true" class="flex-shrink-0 w-14">
                        <!-- Dummy element to force sidebar to shrink to fit close icon -->
                    </div>
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Static sidebar for desktop -->
        <div class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex flex-col flex-grow bg-cyan-700 pt-3 pb-4 overflow-y-auto">
                <div class="flex items-center flex-shrink-0 px-4 space-x-2">
                    <img :alt="page.props.app.name"
                         :src="page.props.app.logo"
                         class="h-8 w-auto" />
                </div>
                <nav aria-label="Sidebar" class="mt-5 flex-1 flex flex-col divide-y divide-cyan-800 overflow-y-auto">
                    <div class="px-2 space-y-1">
                        <NavLink v-for="item in navigation.filter(el => el.visible === undefined || el.visible())" :key="item.name" :route-name="item.route">
                            <component :is="item.icon" aria-hidden="true"
                                       class="mr-4 flex-shrink-0 h-6 w-6 text-cyan-200" />
                            {{ item.name }}
                        </NavLink>
                    </div>
                    <div v-if="page.props.auth.user.is_admin" class="mt-6 pt-6">
                        <div class="px-2 space-y-1">
                            <NavLink v-for="item in secondaryNavigation" v-if="page.props.auth.user.is_admin"
                                     :key="item.name" :route-name="item.route">
                                <component :is="item.icon" aria-hidden="true"
                                           class="mr-4 flex-shrink-0 h-6 w-6 text-cyan-200" />
                                {{ item.name }}
                            </NavLink>
                        </div>
                    </div>
                </nav>
            </div>
        </div>

        <div class="lg:pl-64 flex flex-col flex-1">
            <div class="relative z-10 flex-shrink-0 flex h-16 bg-white border-b border-gray-200 lg:border-none">
                <button
                    class="px-4 border-r border-gray-200 text-gray-400 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-cyan-500 lg:hidden"
                    type="button"
                    @click="sidebarOpen = true">
                    <span class="sr-only">{{ t('message.open_sidebar') }}</span>
                    <Bars3CenterLeftIcon aria-hidden="true" class="h-6 w-6" />
                </button>
                <!-- Search bar -->
                <div class="flex-1 px-4 flex justify-between sm:px-6 lg:mx-auto lg:px-8">
                    <div class="flex-1 flex">

                    </div>
                    <div class="ml-4 flex items-center md:ml-6">
                        <a :href="page.props.app.apk" :download="`${page.props.app.name}.apk`">
                            <ArrowDownTrayIcon aria-hidden="true" class="h-8 w-8 text-gray-400" />
                        </a>

                        <LanguagePicker class="ml-3" />

                        <!-- Profile dropdown -->
                        <Menu as="div" class="ml-3 relative">
                            <div>
                                <MenuButton
                                    class="max-w-xs bg-white rounded-full flex items-center text-sm lg:p-2 lg:rounded-md lg:hover:bg-gray-50">
                                    <img v-if="page.props.jetstream.managesProfilePhotos"
                                         :alt="page.props.auth.user.name"
                                         :src="page.props.auth.user.profile_photo_url"
                                         class="h-8 w-8 rounded-full" />
                                    <span class="hidden ml-3 text-gray-700 text-sm font-medium lg:block"><span
                                        class="sr-only">{{ t('message.user_menu') }}</span>{{ page.props.auth.user.name }}</span>
                                    <ChevronDownIcon aria-hidden="true"
                                                     class="hidden flex-shrink-0 ml-1 h-5 w-5 text-gray-400 lg:block" />
                                </MenuButton>
                            </div>
                            <DropDownTransition>
                                <MenuItems
                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            @click="router.visit(route('profile.show'), { preserveState: false })">
                                            <IdentificationIcon aria-hidden="true"
                                                                class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('page.profile') }}
                                        </button>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                        <button
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            @click="router.visit(route('user.settings.edit'), { preserveState: false })">
                                            <Cog6ToothIcon aria-hidden="true"
                                                           class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('page.settings') }}
                                        </button>
                                    </MenuItem>
                                    <MenuItem v-if="page.props.jetstream.hasApiFeatures" v-slot="{ active }">
                                        <button
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            @click="router.visit(route('api-tokens.index'), { preserveState: false })">
                                            <CodeBracketIcon aria-hidden="true"
                                                             class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('page.api_tokens') }}
                                        </button>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                        <a
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            href="/docs/getting-started.html">
                                            <QuestionMarkCircleIcon aria-hidden="true"
                                                           class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('page.help') }}
                                        </a>
                                    </MenuItem>
                                    <MenuItem v-if="page.props.isImpersonating" v-slot="{ active }">
                                        <button
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            @click="router.visit(route('impersonate.leave'), { preserveState: false })">
                                            <ArrowUpOnSquareIcon aria-hidden="true"
                                                                 class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('action.leave_impersonation') }}
                                        </button>
                                    </MenuItem>
                                    <MenuItem v-else v-slot="{ active }">
                                        <button
                                            :class="[{ ['bg-gray-100']: active }, 'flex items-center px-4 py-2 text-left text-sm text-gray-700 w-full']"
                                            @click="logout">
                                            <ArrowLeftEndOnRectangleIcon aria-hidden="true"
                                                                         class="mr-2 flex-shrink-0 h-5 w-5 text-indigo-600" />
                                            {{ t('action.logout') }}
                                        </button>
                                    </MenuItem>
                                </MenuItems>
                            </DropDownTransition>
                        </Menu>
                    </div>
                </div>
            </div>
            <main class="flex-1">
                <slot />
            </main>
        </div>
    </div>
</template>
