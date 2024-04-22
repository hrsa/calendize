<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, usePage} from '@inertiajs/vue3';
import {computed, ref} from "vue";
import {useDateFormat} from "@vueuse/shared";


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

const events = usePage().props.events.data;
const paginationLinks = usePage().props.events.links;


console.log(usePage().props.events);
console.log(usePage().props.events.links);
console.log(usePage().props.events.links.first);


const downloadIcs = () => {
    window.open(route('event.download', {id: Number(props.eventId), secret: props.eventSecret}));
}

</script>

<template>
    <Head title="Generate"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">My events</h2>
        </template>

        <div class="bg-gray-50 text-black/50 dark:bg-gray-900 dark:text-white/50">
            <div
                class="relative flex min-h-screen flex-col items-center justify-start pt-16 selection:text-white"
            >
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl overflow-hidden rounded-lg bg-white p-6
                            shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300
                            hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3
                            lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700
                            dark:focus-visible:ring-[#FF2D20]">
                    <div class="m-auto grid w-fit grid-cols-3 gap-2 p-6 text-center">
                        <template v-for="event in usePage().props.events.data" :key="event.id">
                            <div class="">{{ useDateFormat(event.created_at, 'HH:mm (DD MMM YYYY)').value }}</div>
                            <div class="max-w-sm">{{ event.summary }}</div>
                            <div v-if="event.ics" class="">
                                <a :href="route('event.download', {id: event.id, secret: event.secret})"
                                   class="inline-flex items-center rounded-md border border-gray-500 bg-green-600/70 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-300 shadow-sm transition duration-150 ease-in-out hover:bg-green-600 disabled:opacity-25"
                                >download ics</a>
                            </div>
                            <div v-else>Error</div>
                        </template>
                    </div>
                    <div v-if="usePage().props.events.links" class="m-auto mt-2 flex justify-center gap-4">
                        <Link v-if="paginationLinks.first" :href="paginationLinks.first"
                              class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            <<
                        </Link>
                        <Link v-if="paginationLinks.prev" :href="paginationLinks.prev"
                              class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            Prev
                        </Link>
                        <Link v-if="paginationLinks.next" :href="paginationLinks.next"
                              class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            Next
                        </Link>
                        <Link v-if="paginationLinks.last" :href="paginationLinks.last"
                              class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            >>
                        </Link>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
