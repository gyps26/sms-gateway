/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

import './bootstrap';
import '../css/app.css';
import '../css/font/inter.css';

import AppLayout from "@/Layouts/AppLayout.vue";
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { useI18n } from '@/Composables/useI18n';

createInertiaApp({
    resolve: (name) => {
        const page = resolvePageComponent(`./Pages/${ name }.vue`, import.meta.glob('./Pages/**/*.vue'));

        page.then(module => {
            const except = ['Home', 'PrivacyPolicy', 'TermsOfService', 'ContactLists/Unsubscribe', 'Payments/Invoice', 'ErrorPage', 'Updated'];
            if (name.startsWith('Auth/') || name.startsWith('Install/') || except.includes(name)) {
                return;
            }

            module.default.layout = module.default.layout || AppLayout;
        });

        return page;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)

        const { t } = useI18n(props.initialPage);
        app.config.globalProperties.$t = t;

        app.mount(el);

        return app;
    }
});
