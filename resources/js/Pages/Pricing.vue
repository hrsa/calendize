<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3"
import { ref } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import LegalFooter from "@/Components/LegalFooter.vue"

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
const email = ref<string>("");
const showNotice = ref<boolean>(true);
const calendarEvent = ref<string>("");
const errorMessage = ref<string>("");
const loading = ref<boolean>(false);
const emailIsUnique = ref<boolean>(false);

const sendCalendarEvent = () => {
    loading.value = true;
    window.axios
        .post(route("guest-generate-calendar"), {
            email: email.value,
            calendarEvent: calendarEvent.value,
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone ?? null,
        })
        .then(() => {
            router.get(route("verification.notice"));
        });
};

const handleClearError = () => {
    errorMessage.value = "";
    emailIsUnique.value = true;
};

const handleEmailExistsError = () => {
    errorMessage.value = "";
    emailIsUnique.value = false;
};

const handleSomethingWentWrongError = () => {
    errorMessage.value = "Something went wrong... I'll fix it soon!";
    emailIsUnique.value = false;
};
</script>

<template>
    <Head>
        <title>Pricing</title>
        <meta
            name="description"
            content="Discover our transparent pricing: 3 subscriptions or bulk credit purchase. Your first 5 credits are free!"
        />
    </Head>
    <GuestLayout>
        <div class="flex flex-col gap-6 lg:gap-8
overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05]
transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20]
 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
            <div>
                <h1 class="mx-auto px-8 text-center text-xl mb-4">
                    My pricing is simple: the more you use Calendize - the better deal i can offer!
                </h1>
                <h3 class="mx-auto px-8 text-center text-sm">
                    All subscriptions can be stopped anytime.
                </h3>
                <h3 class="mx-auto px-8 text-center text-sm">
                    Refunds are possible if none of the credits were used.
                </h3>
            </div>
            <div
                class="flex flex-col items-start gap-6"
            >
                <div class="m-auto flex w-fit flex-wrap items-center gap-6 self-center p-6">
                    <div @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">Beginner</h3>
                            <div class="flex flex-col items-center">
                                <img src="/beginner.png" alt="basic" />
                                <p class="mt-2 text-sm text-gray-300">10 credits / month</p>
                                <p class="mt-2 text-sm text-gray-300">2 revolving credits</p>
                            </div>
                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                1 € / month
                            </div>
                        </div>
                    </div>

                    <div @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">Classic</h3>
                            <div class="flex flex-col items-center">
                                <img src="/classic.png" alt="classic" />
                                <p class="mt-2 text-sm text-gray-300">25 credits / month</p>
                                <p class="mt-2 text-sm text-gray-300">5 revolving credits</p>
                            </div>

                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                2 € / month
                            </div>
                        </div>
                    </div>

                    <div @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">Power</h3>
                            <div class="flex flex-col items-center">
                                <img src="/power.png" alt="power" />
                                <p class="mt-2 text-sm text-gray-300">100 credits / month</p>
                                <p class="mt-2 text-sm text-gray-300">20 revolving credits</p>
                            </div>
                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                5 € / month
                            </div>
                        </div>
                    </div>

                    <div @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">Top up</h3>
                            <div class="flex flex-col items-center">
                                <img src="/credits.png" alt="top-up" />
                                <p class="mt-2 text-sm text-gray-300">5 credits</p>
                                <p class="mt-2 text-sm text-gray-300">valid for 1 year</p>
                            </div>
                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                1 €
                            </div>
                        </div>
                    </div>
                    <LegalFooter />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>
