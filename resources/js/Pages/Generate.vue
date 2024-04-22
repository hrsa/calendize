<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router, usePage} from '@inertiajs/vue3';
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextArea from "@/Components/TextArea.vue";
import {computed, ref} from "vue";
import {IcsEventProcessed} from "@/types";
import SecondaryButton from "@/Components/SecondaryButton.vue";


const calendarEvent = ref<string>('');
const loading = ref<boolean>(false);

const noCreditsLeft = computed(() => {
    return usePage().props.auth.user.credits === 0
});

const props = withDefaults(defineProps<{
    serverErrorMessage?: string;
    serverSuccess?: string;
    eventId?: string;
    eventSecret?: string;
}>(), {
    serverErrorMessage: '',
    serverSuccess: '',
    eventId: null,
    eventSecret: ''
});

const sendCalendarEvent = () => {


    loading.value = true;
    clearNotifications(true, true);

    window.axios.post(route('generate-calendar'),
        {
            "email": usePage().props.auth.user.email,
            "calendarEvent": calendarEvent.value,
            "timeZone": Intl.DateTimeFormat().resolvedOptions().timeZone ?? null
        })
        .then(res => {
            console.log(res.data.icsId);
            window.Echo.private('ics-event-' + res.data.icsId)
                .listen('IcsEventProcessed', (event: IcsEventProcessed) => {
                    console.log(event);
                    router.get(route('generate'), {
                        serverErrorMessage: event.error,
                        serverSuccess: event.summary,
                        eventId: event.id,
                        eventSecret: event.secret,
                    }, {preserveState: true, preserveScroll: true});
                    loading.value = false;
                })
        })
        .catch(error => {
            console.error(error);
            router.get(route('generate'), {
                        serverErrorMessage: error.response.data.error,
                    }, {preserveState: true, preserveScroll: true});
            loading.value = false;
        });
}

const clearNotifications = (clearError: boolean, clearSuccess: boolean) => {
    router.get(route('generate'), {
            serverErrorMessage: clearError ? '' : props.serverErrorMessage,
            serverSuccess: clearSuccess ? '' : props.serverSuccess,
            eventId: null,
            eventSecret: null,
        },
        {preserveState: true, preserveScroll: true});
}

const downloadIcs = () => {
    window.open(route('event.download', {id: Number(props.eventId), secret: props.eventSecret}));
}

</script>

<template>
    <Head title="Generate"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-center text-gray-800 dark:text-gray-200">Generate</h2>
        </template>

        <div class="bg-gray-50 text-black/50 dark:bg-gray-900 dark:text-white/50">
            <div
                class="relative min-h-screen flex flex-col items-center pt-16 justify-start selection:text-white"
            >
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div
                            id="docs-card"
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6
                            shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300
                            hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3
                            lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700
                            dark:focus-visible:ring-[#FF2D20]"
                        >
                            <div class="flex flex-col gap-6 mx-auto">
                                <h3
                                v-if="props.serverErrorMessage"
                                @click="clearNotifications(true, false)"
                                class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-red-600/50 bg-red-600/50 px-8 py-2 text-center text-white"
                            >{{ props.serverErrorMessage }}</h3>

                                <h3 v-if="props.serverSuccess"
                                    @click="clearNotifications(false, true)"
                                    class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-green-600/50 bg-green-600/50 px-8 py-2 text-center text-white"
                                >{{ props.serverSuccess }}</h3>

                                <SecondaryButton
                                    v-if="props.eventId && props.eventSecret && props.serverSuccess"
                                    class="mx-auto" @click="downloadIcs">
                                    Download ICS
                                </SecondaryButton>
                                <h3 v-if="noCreditsLeft"
                                @click="console.log('create checkout page')"
                                class="mx-auto max-w-sm cursor-pointer rounded-xl border-2 border-orange-600/50 bg-orange-600/50 px-8 py-2 text-center text-white"
                            >You have no credits left! Let's get you some more...</h3>
                            </div>
                            <div v-if="!noCreditsLeft"
                                class="relative h-48 w-full">
                                <TextArea class="h-48 w-full resize-none text-lg" :class="loading ? 'blur-lg' : ''"
                                          placeholder="Share your calendar details!"
                                          v-model="calendarEvent"/>
                                <div v-if="loading"
                                     role="status"
                                     class="absolute top-2/4 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                    <LoadingSpinner/>
                                </div>
                            </div>
                            <div v-if="!noCreditsLeft"
                                class="m-auto mt-4 flex items-center justify-end">
                                <PrimaryButton v-if="!loading" :class="{ 'opacity-25': !calendarEvent }"
                                               :disabled="!calendarEvent"
                                               @click="sendCalendarEvent"
                                >
                                    Generate ({{ $page.props.auth.user.credits }} credits remaining)
                                </PrimaryButton>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
