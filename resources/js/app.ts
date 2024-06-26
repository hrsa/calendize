import "./bootstrap";
import "../css/app.css";

import { createApp, DefineComponent, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { defaultConfig, plugin as formkitPlugin } from "@formkit/vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";
import { i18nVue } from "laravel-vue-i18n"
import { LanguageJsonFileInterface } from "laravel-vue-i18n/interfaces/language-json-file"

const appName = import.meta.env.VITE_APP_NAME || "Calendize";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>("./Pages/**/*.vue")),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(formkitPlugin, defaultConfig)
            .use(i18nVue, {
                resolve: async (lang: string): Promise<LanguageJsonFileInterface> => {
                    const langs = import.meta.glob("../../lang/*.json");
                    return await langs[`../../lang/php_${lang}.json`]() as LanguageJsonFileInterface;
                },
            })
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
