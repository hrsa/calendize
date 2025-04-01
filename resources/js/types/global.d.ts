import { Page, PageProps as InertiaPageProps } from "@inertiajs/core";
import { AxiosInstance } from "axios";
import { route as ziggyRoute } from "ziggy-js";
import { PageProps as AppPageProps } from "./";
import { trans, transChoice } from "laravel-vue-i18n";

declare global {
    interface Window {
        axios: AxiosInstance;
        Echo: {
            private(channel: string): any;
        };
    }

    const route: typeof ziggyRoute;
}

declare module "@vue/runtime-core" {
    interface ComponentCustomProperties {
        route: typeof ziggyRoute;
        $page: Page;
        $t: typeof trans;
        $tChoice: typeof transChoice;
    }
}

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {
    }
}
