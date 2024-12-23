import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import InstantSearch from 'vue-instantsearch/vue3/es';
import PrimeVue from 'primevue/config';
import { QuillEditor } from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Toast, {POSITION,PluginOptions } from "vue-toastification";
// Import the CSS or use your own!
import "vue-toastification/dist/index.css";
import Aura from '@primevue/themes/aura';
import { Button, Drawer, ToggleSwitch } from 'primevue';
import Inplace from 'primevue/inplace';
import ProgressBar from 'primevue/progressbar';



const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('QuillEditor', QuillEditor)
            .use(InstantSearch)
            .use(PrimeVue, {
                theme: {
                    preset: Aura
                }})
            .component('Button', Button)
            .component('Drawer', Drawer)
            .component('ToggleSwitch', ToggleSwitch)
            .component('ProgressBar', ProgressBar)
            .component('Inplace', Inplace)
            .use(Toast, {
                position: POSITION.BOTTOM_RIGHT,
                timeout: 3000
            } as PluginOptions)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
