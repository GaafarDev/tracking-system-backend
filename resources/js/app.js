import './bootstrap';
import '../css/app.css';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { MotionPlugin } from '@vueuse/motion'

// Fix Leaflet's icon paths
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.mergeOptions({
    iconRetinaUrl: '/images/vendor/leaflet/dist/marker-icon-2x.png',
    iconUrl: '/images/vendor/leaflet/dist/marker-icon.png',
    shadowUrl: '/images/vendor/leaflet/dist/marker-shadow.png',
});

// Make Leaflet available globally if needed
window.L = L;

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Toast, {
                transition: "Vue-Transition",
                maxToasts: 20,
                newestOnTop: true
            })
            .use(MotionPlugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
    // Enable scroll position preservation
    preserveScroll: true,
});