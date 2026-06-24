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
import AppHead from "@/Components/AppHead.vue";
import DialogModal from "@/Components/DialogModal.vue";
import LanguagePicker from '@/Components/LanguagePicker.vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { useI18n } from '@/Composables/useI18n.js';
import Pricing from "@/Pages/Plans/Pricing.vue";
import { Dialog, DialogPanel } from '@headlessui/vue';
import {
    ArrowUturnLeftIcon,
    Bars3Icon,
    CodeBracketIcon,
    ListBulletIcon,
    MegaphoneIcon,
    PhoneArrowUpRightIcon,
    PhoneIcon,
    ScaleIcon,
    ServerStackIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    announcement: {
        type: String,
        default: null,
    },
    plans: {
        type: Array,
        required: true,
    },
    subscription: {
        type: Object,
        default: null,
    },
    supportUrl: {
        type: String,
        default: null,
    },
});

const { t } = useI18n();
const showAnnouncement = ref(props.announcement !== null);

const navigation = [
    { name: t('message.home.features.title'), href: '#features' },
    { name: t('message.pricing.title'), href: '#pricing', visible: props.plans.length > 0 },
    { name: t('message.home.faqs.title'), href: '#faqs' },
    { name: t('message.home.documentation'), href: '/docs/getting-started.html' },
    { name: 'API', href: '/docs/api' },
];

const features = [
    {
        name: t('message.home.features.1.title'),
        description: t('message.home.features.1.description'),
        icon: ListBulletIcon,
    },
    {
        name: t('message.home.features.2.title'),
        description: t('message.home.features.2.description'),
        icon: ArrowUturnLeftIcon,
    },
    {
        name: t('message.home.features.3.title'),
        description: t('message.home.features.3.description'),
        icon: CodeBracketIcon,
    },
    {
        name: t('message.home.features.4.title'),
        description: t('message.home.features.4.description'),
        icon: ServerStackIcon,
    },
    {
        name: t('message.home.features.5.title'),
        description: t('message.home.features.5.description'),
        icon: PhoneArrowUpRightIcon,
    },
    {
        name: t('message.home.features.6.title'),
        description: t('message.home.features.6.description'),
        icon: ScaleIcon,
    },
    {
        name: t('message.home.features.7.title'),
        description: t('message.home.features.7.description'),
        icon: PhoneIcon,
    },
    {
        name: t('message.home.features.8.title'),
        description: t('message.home.features.8.description'),
        icon: MegaphoneIcon,
    },
];

const faqs = [
    {
        id: 1,
        question: t('message.home.faqs.1.question'),
        answer: t('message.home.faqs.1.answer'),
    },
    {
        id: 2,
        question: t('message.home.faqs.2.question'),
        answer: t('message.home.faqs.2.answer'),
    },
    {
        id: 3,
        question: t('message.home.faqs.3.question'),
        answer: t('message.home.faqs.3.answer'),
    },
    {
        id: 4,
        question: t('message.home.faqs.4.question'),
        answer: t('message.home.faqs.4.answer'),
    },
    {
        id: 5,
        question: t('message.home.faqs.5.question'),
        answer: t('message.home.faqs.5.answer'),
    },
];

const footerNavigation = {
    sections: [
        { name: t('message.home.features.title'), href: '#features' },
        { name: t('message.pricing.title'), href: '#pricing', visible: props.plans.length > 0 },
        { name: t('message.home.faqs.title'), href: '#faqs' },
    ],
    support: [
        { name: t('message.home.contact'), href: props.supportUrl },
        { name: t('message.home.documentation'), href: '/docs/getting-started.html' },
    ],
    developers: [
        { name: 'API', href: '/docs/api' },
    ],
    legal: [
        { name: t('message.home.tos'), href: route('terms.show') },
        { name: t('message.home.privacy_policy'), href: route('policy.show') },
    ],
};

const mobileMenuOpen = ref(false);
</script>

<template>
    <AppHead title="Home" />
    <div class="bg-white">
        <!-- Header -->
        <header class="absolute inset-x-0 top-0 z-50">
            <nav aria-label="Global" class="flex items-center justify-between p-6 lg:px-8">
                <div class="flex lg:flex-1">
                    <a class="-m-1.5 p-1.5" href="#">
                        <span class="sr-only">{{ $page.props.app.name }}</span>
                        <img :alt="$page.props.app.name" :src="$page.props.app.logo"
                             class="h-8 w-auto" />
                    </a>
                </div>
                <div class="flex lg:hidden">
                    <button class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                            type="button"
                            @click="mobileMenuOpen = true">
                        <span class="sr-only">{{ t('message.home.open_main_menu') }}</span>
                        <Bars3Icon aria-hidden="true" class="size-6" />
                    </button>
                </div>
                <div class="hidden lg:flex lg:gap-x-12">
                    <template v-for="item in navigation" :key="item.name">
                        <a v-if="item.visible === undefined || item.visible" :href="item.href"
                           class="text-sm/6 font-semibold text-gray-900">{{ item.name }}</a>
                    </template>
                </div>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end items-center space-x-4">
                    <div>
                        <LanguagePicker />
                    </div>
                    <div>
                        <Link v-if="$page.props.auth.user"
                              :href="route('dashboard')"
                              class="text-sm/6 font-semibold text-gray-900">
                            {{ t('page.dashboard') }} <span aria-hidden="true">&rarr;</span>
                        </Link>
                        <Link v-else
                              :href="route('login')" class="text-sm/6 font-semibold text-gray-900">
                            {{ t('page.login') }}
                            <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </div>
                </div>
            </nav>
            <Dialog :open="mobileMenuOpen" class="lg:hidden" @close="mobileMenuOpen = false">
                <div class="fixed inset-0 z-50" />
                <DialogPanel
                    class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                    <div class="flex items-center justify-between">
                        <a class="-m-1.5 p-1.5" href="#">
                            <span class="sr-only">{{ $page.props.app.name }}</span>
                            <img :src="$page.props.app.logo" alt="$page.props.app.name"
                                 class="h-8 w-auto" />
                        </a>
                        <button class="-m-2.5 rounded-md p-2.5 text-gray-700" type="button"
                                @click="mobileMenuOpen = false">
                            <span class="sr-only">{{ t('message.home.close_menu') }}</span>
                            <XMarkIcon aria-hidden="true" class="size-6" />
                        </button>
                    </div>
                    <div class="mt-6 flow-root">
                        <div class="-my-6 divide-y divide-gray-500/10">
                            <div class="py-6">
                                <LanguagePicker />
                            </div>
                            <div class="space-y-2 py-6">
                                <template v-for="item in navigation" :key="item.name">
                                    <a v-if="item.visible === undefined || item.visible"
                                       :href="item.href"
                                       class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">
                                        {{ item.name }}
                                    </a>
                                </template>
                            </div>
                            <div class="py-6">
                                <Link v-if="$page.props.auth.user"
                                      :href="route('dashboard')"
                                      class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">
                                    {{ t('page.dashboard') }}
                                </Link>
                                <Link v-else
                                      :href="route('login')"
                                      class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">
                                    {{ t('page.login') }}
                                </Link>
                            </div>
                        </div>
                    </div>
                </DialogPanel>
            </Dialog>
        </header>

        <main class="isolate">
            <!-- Hero section -->
            <div class="relative pt-14">
                <div aria-hidden="true"
                     class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
                    <div
                        class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
                </div>
                <div class="py-24 sm:py-32 lg:pb-40">
                    <div class="mx-auto max-w-7xl px-6 lg:px-8">
                        <div class="mx-auto max-w-2xl text-center">
                            <h1 class="text-balance text-5xl font-semibold tracking-tight text-gray-900 sm:text-7xl">
                                {{ $page.props.app.name }}
                            </h1>
                            <p class="mt-8 text-pretty text-lg font-medium text-gray-500 sm:text-xl/8">
                                {{ t('message.home.tagline') }}
                            </p>
                            <div class="mt-10 flex items-center justify-center gap-x-6">
                                <a v-if="$page.props.canRegister"
                                   :href="route('register')"
                                   class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    {{ t('message.home.get_started') }}
                                </a>
                                <a class="text-sm/6 font-semibold text-gray-900"
                                   href="#features">{{ t('message.home.learn_more') }} <span
                                    aria-hidden="true">→</span></a>
                            </div>
                        </div>
                        <div class="mt-16 flow-root sm:mt-24">
                            <div
                                class="-m-2 rounded-xl bg-gray-900/5 p-2 ring-1 ring-inset ring-gray-900/10 lg:-m-4 lg:rounded-2xl lg:p-4">
                                <img alt="App screenshot"
                                     class="rounded-md shadow-2xl ring-1 ring-gray-900/10" height="1442"
                                     src="/images/app.png"
                                     width="2432" />
                            </div>
                        </div>
                    </div>
                </div>
                <div
                    aria-hidden="true"
                    class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]">
                    <div
                        class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
                </div>
            </div>

            <!-- Feature section -->
            <div id="features" class="mx-auto mt-24 max-w-7xl px-6 sm:mt-48 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base/7 font-semibold text-indigo-600">{{ t('message.home.features.title') }}</h2>
                    <p class="mt-2 text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl lg:text-balance">
                        {{ t('message.home.features.tagline') }}
                    </p>
                    <p class="mt-6 text-pretty text-lg/8 text-gray-600">
                        {{ t('message.home.features.description') }}
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-4xl">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-10 lg:max-w-none lg:grid-cols-2 lg:gap-y-16">
                        <div v-for="feature in features" :key="feature.name" class="relative pl-16">
                            <dt class="text-base/7 font-semibold text-gray-900">
                                <div
                                    class="absolute left-0 top-0 flex size-10 items-center justify-center rounded-lg bg-indigo-600">
                                    <component :is="feature.icon" aria-hidden="true" class="size-6 text-white" />
                                </div>
                                {{ feature.name }}
                            </dt>
                            <dd class="mt-2 text-base/7 text-gray-600">{{ feature.description }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Testimonial section -->
            <div class="mx-auto mt-32 max-w-7xl sm:mt-56 sm:px-6 lg:px-8">
                <div
                    class="relative overflow-hidden bg-gray-900 px-6 py-20 shadow-xl sm:rounded-3xl sm:px-10 sm:py-24 md:px-12 lg:px-20">
                    <img alt=""
                         class="absolute inset-0 size-full object-cover brightness-150 saturate-0"
                         src="https://images.unsplash.com/photo-1601381718415-a05fb0a261f3?ixid=MXwxMjA3fDB8MHxwcm9maWxlLXBhZ2V8ODl8fHxlbnwwfHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1216&q=80" />
                    <div class="absolute inset-0 bg-gray-900/90 mix-blend-multiply" />
                    <div aria-hidden="true" class="absolute -left-80 -top-56 transform-gpu blur-3xl">
                        <div
                            class="aspect-[1097/845] w-[68.5625rem] bg-gradient-to-r from-[#ff4694] to-[#776fff] opacity-[0.45]"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
                    </div>
                    <div aria-hidden="true"
                         class="hidden md:absolute md:bottom-16 md:left-[50rem] md:block md:transform-gpu md:blur-3xl">
                        <div
                            class="aspect-[1097/845] w-[68.5625rem] bg-gradient-to-r from-[#ff4694] to-[#776fff] opacity-25"
                            style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
                    </div>
                    <div class="relative mx-auto max-w-2xl lg:mx-0">
                        <img :alt="$page.props.app.name" :src="$page.props.app.logo"
                             class="h-12 w-auto" />
                        <figure>
                            <blockquote class="mt-6 text-lg font-semibold text-white sm:text-xl/8">
                                <p>“The program works really fine, after years trying to find the right
                                    product for sending massive and customized sms to customers, I finally came across
                                    this app and It's really doing the job perfectly.”</p>
                            </blockquote>
                            <figcaption class="mt-6 text-base text-white">
                                <div class="font-semibold">Stefano Galoppo</div>
                            </figcaption>
                        </figure>
                    </div>
                </div>
            </div>

            <!-- Pricing section -->
            <div v-if="plans.length > 0" id="pricing" class="py-24 sm:pt-48">
                <Pricing :plans="plans" :subscription="subscription" />
            </div>

            <!-- FAQs -->
            <div
                id="faqs"
                class="mx-auto max-w-2xl divide-y divide-gray-900/10 px-6 pb-8 sm:pb-24 sm:pt-12 lg:max-w-7xl lg:px-8 lg:pb-32">
                <h2 class="text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
                    {{ t('message.home.faqs.description') }}
                </h2>
                <dl class="mt-10 space-y-8 divide-y divide-gray-900/10">
                    <div v-for="faq in faqs" :key="faq.id" class="pt-8 lg:grid lg:grid-cols-12 lg:gap-8">
                        <dt class="text-base/7 font-semibold text-gray-900 lg:col-span-5">{{ faq.question }}</dt>
                        <dd class="mt-4 lg:col-span-7 lg:mt-0">
                            <p class="text-base/7 text-gray-600">{{ faq.answer }}</p>
                        </dd>
                    </div>
                </dl>
            </div>

            <!-- CTA section -->
            <div class="relative -z-10 mt-32 px-6 lg:px-8">
                <div
                    aria-hidden="true"
                    class="absolute inset-x-0 top-1/2 -z-10 flex -translate-y-1/2 transform-gpu justify-center overflow-hidden blur-3xl sm:bottom-0 sm:right-[calc(50%-6rem)] sm:top-auto sm:translate-y-0 sm:transform-gpu sm:justify-end">
                    <div
                        class="aspect-[1108/632] w-[69.25rem] flex-none bg-gradient-to-r from-[#ff80b5] to-[#9089fc] opacity-25"
                        style="clip-path: polygon(73.6% 48.6%, 91.7% 88.5%, 100% 53.9%, 97.4% 18.1%, 92.5% 15.4%, 75.7% 36.3%, 55.3% 52.8%, 46.5% 50.9%, 45% 37.4%, 50.3% 13.1%, 21.3% 36.2%, 0.1% 0.1%, 5.4% 49.1%, 21.4% 36.4%, 58.9% 100%, 73.6% 48.6%)" />
                </div>
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-balance text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">
                        {{ t('message.home.footer.title') }}
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-pretty text-lg/8 text-gray-600">
                        {{ t('message.home.footer.tagline') }}
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a v-if="$page.props.canRegister"
                           :href="route('register')"
                           class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            {{ t('message.home.get_started') }}
                        </a>
                        <a class="text-sm/6 font-semibold text-gray-900"
                           href="#features">{{ t('message.home.learn_more') }} <span
                            aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div
                    aria-hidden="true"
                    class="absolute left-1/2 right-0 top-full -z-10 hidden -translate-y-1/2 transform-gpu overflow-hidden blur-3xl sm:block">
                    <div
                        class="aspect-[1155/678] w-[72.1875rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-30"
                        style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="relative mx-auto mt-32 max-w-7xl px-6 lg:px-8">
            <div class="border-t border-gray-900/10 py-16 sm:py-24 lg:py-32">
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
                    <img :alt="$page.props.app.name" :src="$page.props.app.logo"
                         class="h-9" />
                    <div class="mt-16 grid grid-cols-2 gap-8 xl:col-span-2 xl:mt-0">
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm/6 font-semibold text-gray-900">{{ t('message.home.sections') }}</h3>
                                <ul class="mt-6 space-y-4" role="list">
                                    <li v-for="item in footerNavigation.sections" :key="item.name">
                                        <a v-if="item.visible === undefined || item.visible"
                                           :href="item.href"
                                           class="text-sm/6 text-gray-600 hover:text-gray-900">
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-10 md:mt-0">
                                <h3 class="text-sm/6 font-semibold text-gray-900">{{ t('message.home.support') }}</h3>
                                <ul class="mt-6 space-y-4" role="list">
                                    <li v-for="item in footerNavigation.support" :key="item.name">
                                        <a :href="item.href"
                                           class="text-sm/6 text-gray-600 hover:text-gray-900"
                                           target="_blank">
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="md:grid md:grid-cols-2 md:gap-8">
                            <div>
                                <h3 class="text-sm/6 font-semibold text-gray-900">
                                    {{ t('message.home.developers') }}
                                </h3>
                                <ul class="mt-6 space-y-4" role="list">
                                    <li v-for="item in footerNavigation.developers" :key="item.name">
                                        <a :href="item.href"
                                           class="text-sm/6 text-gray-600 hover:text-gray-900"
                                           target="_blank">
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="mt-10 md:mt-0">
                                <h3 class="text-sm/6 font-semibold text-gray-900">{{ t('message.home.legal') }}</h3>
                                <ul class="mt-6 space-y-4" role="list">
                                    <li v-for="item in footerNavigation.legal" :key="item.name">
                                        <a :href="item.href"
                                           class="text-sm/6 text-gray-600 hover:text-gray-900"
                                           target="_blank">
                                            {{ item.name }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <DialogModal :show="showAnnouncement" @close="showAnnouncement = false">
        <template #title>
            <div class="flex items-center space-x-2">
                <MegaphoneIcon aria-hidden="true" class="size-8 text-blue-400" />
                <span>{{ t('message.announcement') }}</span>
            </div>
        </template>

        <template #content>
            <div class="whitespace-pre-wrap">{{ announcement }}</div>
        </template>

        <template #footer>
            <SecondaryButton @click="showAnnouncement = false">
                {{ t('action.close') }}
            </SecondaryButton>
        </template>
    </DialogModal>
</template>
