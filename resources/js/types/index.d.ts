import { Config } from 'ziggy-js';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string;
    credits: number;
    failed_requests: number;
    blocked: boolean;
}

export interface IcsEventProcessed extends Event {
  id: number;
  ics: string;
  error: string;
}

declare global {
  interface Window {
    Echo: {
      private(channel: string): any;
    };
  }
}

export type PageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
};
