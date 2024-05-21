<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { computed, ref } from "vue";
import { IcsEventProcessed } from "@/types";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import EventGenerationTextArea from "@/Components/EventGenerationTextArea.vue";

const calendarEvent = ref<string>("");
const loading = ref<boolean>(false);

const noCreditsLeft = computed(() => {
    return usePage().props.auth.user.credits === 0;
});

const props = withDefaults(
    defineProps<{
        serverErrorMessage?: string;
        serverSuccess?: string;
        eventId?: string | null;
        eventSecret?: string;
    }>(),
    {
        serverErrorMessage: "",
        serverSuccess: "",
        eventId: null,
        eventSecret: "",
    },
);

const sendCalendarEvent = () => {
    loading.value = true;
    clearNotifications(true, true);

    window.axios
        .post(route("generate-calendar"), {
            email: usePage().props.auth.user.email,
            calendarEvent: calendarEvent.value,
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone ?? null,
        })
        .then((res) => {
            window.Echo.private("ics-event-" + res.data.icsId).listen("IcsEventProcessed", (event: IcsEventProcessed) => {
                router.get(
                    route("generate"),
                    {
                        serverErrorMessage: event.error,
                        serverSuccess: event.summary,
                        eventId: event.id,
                        eventSecret: event.secret,
                    },
                    { preserveState: true, preserveScroll: true },
                );
                loading.value = false;
            });
        })
        .catch((error) => {
            console.error(error);
            router.get(
                route("generate"),
                {
                    serverErrorMessage: error.response.data.error,
                },
                { preserveState: true, preserveScroll: true },
            );
            loading.value = false;
        });
};

const clearNotifications = (clearError: boolean, clearSuccess: boolean) => {
    router.get(
        route("generate"),
        {
            serverErrorMessage: clearError ? "" : props.serverErrorMessage,
            serverSuccess: clearSuccess ? "" : props.serverSuccess,
            eventId: null,
            eventSecret: null,
        },
        { preserveState: true, preserveScroll: true },
    );
};

const downloadIcs = () => {
    window.open(route("event.download", { id: Number(props.eventId), secret: props.eventSecret }));
};
</script>

<template>
    <Head>
        <title>Calendize an event</title>
        <meta name="description" content="Generate and manage calendar events effortlessly with our intuitive solution. Paste your event details or email us to calendize. Download ICS files easily and get them by email." />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Calendize</h1>
        </template>

        <div class="text-black/50 dark:text-white/50">
            <div class="relative flex min-h-screen flex-col items-center justify-start pt-16 selection:text-white">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <h3 class="mx-auto px-2 text-center text-lg sm:text-xl">
                                I'm ready to calendize everything that you paste here!<br />
                                Don't forget - you can forward me an email you want to calendize:
                                <a class="font-semibold underline" href="mailto:hey@calendize.it">hey@calendize.it</a>
                            </h3>
                            <div class="mx-auto flex flex-col gap-6">
                                <h3
                                    v-if="props.serverErrorMessage"
                                    @click="clearNotifications(true, false)"
                                    class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-red-600/50 bg-red-600/50 px-8 py-2 text-center text-white"
                                >
                                    {{ props.serverErrorMessage }}
                                </h3>

                                <h3
                                    v-if="props.serverSuccess"
                                    @click="clearNotifications(false, true)"
                                    class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-green-600/50 bg-green-600/50 px-8 py-2 text-center text-white"
                                >
                                    {{ props.serverSuccess }}
                                </h3>

                                <SecondaryButton
                                    v-if="props.eventId && props.eventSecret && props.serverSuccess"
                                    class="mx-auto"
                                    @click="downloadIcs"
                                >
                                    Download ICS
                                </SecondaryButton>
                                <Link
                                    v-if="noCreditsLeft"
                                    :href="route('dashboard')"
                                    class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-orange-600/50 bg-orange-600/50 px-8 py-2 text-center text-white"
                                    >You have no credits left! Let's get you some more...
                                </Link>
                            </div>
                            <EventGenerationTextArea v-model="calendarEvent" :loading v-if="!noCreditsLeft" />
                            <div v-if="!noCreditsLeft" class="m-auto mt-4 flex items-center justify-end">
                                <PrimaryButton
                                    v-if="!loading"
                                    :class="{ 'opacity-25': !calendarEvent }"
                                    :disabled="!calendarEvent"
                                    @click="sendCalendarEvent"
                                >
                                    Calendize ({{ $page.props.auth.user.credits }} {{ $page.props.auth.user.credits === 1 ? 'credit' : 'credits' }} remaining)
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
