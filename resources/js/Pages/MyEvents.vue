<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useDateFormat } from "@vueuse/shared";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { IcsEvent } from "@/types";
import FeedbackForm from "@/Components/FeedbackForm.vue";
import Modal from "@/Components/Modal.vue";
import { ref } from "vue";

const feedbackOpen = ref(false);

type Links = {
    first: string | null;
    prev: string | null;
    next: string | null;
    last: string | null;
};

interface EventPagination {
    data: IcsEvent[];
    links: Links;
}

const events = (usePage().props.events as EventPagination).data as IcsEvent[];
const feedbackEvent = ref<IcsEvent | null>(null);
const paginationLinks = (usePage().props.events as EventPagination).links as Links;
</script>

<template>
    <Head>
        <title>{{ $t("my-events.title") }}</title>
        <meta name="description" :content="$t('my-events.meta')" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                {{ $t("my-events.title") }}
            </h1>
        </template>

        <div class="text-black/50 dark:text-white/50">
            <div class="relative flex min-h-screen flex-col items-center justify-start pt-16 selection:text-white">
                <div
                    class="relative w-full max-w-2xl overflow-hidden rounded-lg bg-white p-6 px-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05]
                        transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] md:row-span-3 lg:max-w-7xl
                        lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                >
                    <div v-if="!events.length" class="m-auto flex w-fit flex-col gap-2 p-6 text-center">
                        <p class="max-w-sm text-xl">
                            {{ $t("my-events.description") }}
                        </p>
                        <PrimaryButton @click="router.get(route('generate'))" class="m-auto mt-4 w-fit">{{
                                $t("my-events.start")
                            }}
                        </PrimaryButton>
                    </div>
                    <div v-else class="m-auto grid w-fit grid-cols-4 gap-2 p-6 text-center">
                        <template v-for="event in events" :key="event.id">
                            <div class="content-center">
                                {{ useDateFormat(event.created_at, "HH:mm (DD MMM YYYY)").value }}
                            </div>
                            <div class="max-w-sm content-center">
                                {{ event.summary }}
                            </div>
                            <div v-if="event.ics" class="content-center">
                                <a
                                    :href="
                                        route('event.download', {
                                            id: event.id,
                                            secret: event.secret,
                                        })
                                    "
                                    class="inline-flex items-center rounded-md bg-green-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition
                                        duration-150 ease-in-out hover:bg-green-600 hover:text-gray-50 disabled:opacity-25 dark:text-gray-200"
                                >{{ $t("my-events.download") }}</a
                                >
                            </div>
                            <div v-else>
                                <span
                                    class="inline-flex items-center rounded-md border border-gray-500 bg-red-600/70 px-4 py-2 text-xs font-semibold uppercase tracking-widest
                                        text-gray-300 shadow-sm transition duration-150 ease-in-out"
                                >{{ $t("my-events.error") }}</span
                                >
                            </div>

                            <div @click="feedbackEvent = event; feedbackOpen = true">
                                <img
                                    class="rounded-md max-h-10 cursor-pointer text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition
                                        duration-150 ease-in-out hover:text-gray-50 disabled:opacity-25 dark:text-gray-200"
                                    src="/feedback.png"  alt="feedback"/>
                            </div>

                            <Modal :show="feedbackOpen" @close="feedbackOpen = false">
                                <div class="p-6">
                                    <FeedbackForm v-if="feedbackEvent"  :event="feedbackEvent" @feedback-sent="feedbackOpen = false" />
                                </div>
                            </Modal>
                        </template>
                    </div>

                    <div v-if="paginationLinks" class="m-auto mt-2 flex justify-center gap-4">
                        <Link
                            v-if="paginationLinks.first && paginationLinks.first !== paginationLinks.last"
                            :href="paginationLinks.first"
                            class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            <<
                        </Link>
                        <Link
                            v-if="paginationLinks.prev"
                            :href="paginationLinks.prev"
                            class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            {{ $t("my-events.links.previous") }}
                        </Link>
                        <Link
                            v-if="paginationLinks.next"
                            :href="paginationLinks.next"
                            class="rounded-lg border-2 border-gray-100/50 px-2 py-1 hover:border-gray-400 active:bg-gray-100 active:text-gray-500"
                        >
                            {{ $t("my-events.links.next") }}
                        </Link>
                        <Link
                            v-if="paginationLinks.last && paginationLinks.first !== paginationLinks.last"
                            :href="paginationLinks.last"
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
