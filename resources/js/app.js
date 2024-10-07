import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import MainLayout from './Layouts/MainLayout.vue';
import '../css/app.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true }) //make new folder "Pages"
        const page = pages[`./Pages/${name}.vue`]
        if (!page.default.layout) { //checking(if this page does not have any specified layout)
            page.default.layout = MainLayout //and setting default layout
        }

        return page
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})