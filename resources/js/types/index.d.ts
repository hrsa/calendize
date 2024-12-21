import { Config } from "ziggy-js";

export type User = {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    credits: number;
    rollover_credits: number | undefined;
    failed_requests: number;
    has_password: boolean;
    is_admin: boolean;
    active_subscription: string;
    hide_pw_reminder: string | undefined;
    days_since_password_reminder: number;
};

export type IcsEvent = {
    id: number;
    summary: string;
    error: string | null;
    ics: string | null;
    created_at: string;
    token_usage: number | null;
    secret: string;
};

export type IcsEventProcessed = {
    id: number;
    ics: string;
    error: string;
    secret: string;
    summary: string;
};

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };

    ziggy: Config & { location: string };
};
