import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import Layout from "./Shared/layouts/Layout.vue";
import {resolvePageComponent} from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from 'ziggy-js';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async name => {
        const pages = await resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob("./Pages/**/*.vue"));
        pages.default.layout ??= Layout;
        return pages;
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el)
    },
})
