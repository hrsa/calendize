import { PageProps as InertiaPageProps } from "@inertiajs/core";
import { Page } from "@inertiajs/core";
import { AxiosInstance } from "axios";
import { route as ziggyRoute } from "@types/ziggy-js";
import { PageProps as AppPageProps } from "./";

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
    }
}

declare module "@inertiajs/core" {
    interface PageProps extends InertiaPageProps, AppPageProps {
    }
}
