<script setup lang="ts">
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import LegalFooter from "@/Components/LegalFooter.vue";

</script>

<template>
    <Head>
        <title>{{ $t("dashboard.pricing.title") }}</title>
        <meta name="description" :content="$t('dashboard.pricing.meta')" />
    </Head>
    <GuestLayout>
        <div
            class="flex flex-col gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:gap-8 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
        >
            <div>
                <h1 class="mx-auto mb-4 px-8 text-center text-xl">
                    {{ $t("dashboard.pricing.description.main") }}
                </h1>
                <h3 class="mx-auto px-8 text-center text-sm">
                    {{ $t("dashboard.pricing.description.cancellation") }}
                </h3>
                <h3 class="mx-auto px-8 text-center text-sm">
                    {{ $t("dashboard.pricing.description.refunds") }}
                </h3>
            </div>
            <div class="flex flex-col items-start gap-6">
                <div class="m-auto flex w-fit flex-wrap items-center gap-6 self-center p-6">
                    <div
                        @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.subscriptions.beginner") }}
                            </h3>
                            <div class="flex flex-col items-center">
                                <img src="/beginner.png" alt="basic" />
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.credits-per-month", { credits: "10" }) }}
                                </p>
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.revolving-credits", { credits: "2" }) }}
                                </p>
                            </div>
                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                {{ $t("dashboard.subscriptions.price", { price: "1" }) }}
                            </div>
                        </div>
                    </div>

                    <div
                        @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.subscriptions.classic") }}
                            </h3>
                            <div class="flex flex-col items-center">
                                <img src="/classic.png" alt="classic" />
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.credits-per-month", { credits: "25" }) }}
                                </p>
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.revolving-credits", { credits: "5" }) }}
                                </p>
                            </div>

                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                {{ $t("dashboard.subscriptions.price", { price: "2" }) }}
                            </div>
                        </div>
                    </div>

                    <div
                        @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.subscriptions.power") }}
                            </h3>
                            <div class="flex flex-col items-center">
                                <img src="/power.png" alt="power" />
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.credits-per-month", { credits: "100" }) }}
                                </p>
                                <p class="mt-2 text-sm text-gray-300">
                                    {{ $t("dashboard.subscriptions.revolving-credits", { credits: "20" }) }}
                                </p>
                            </div>
                            <div
                                class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                            >
                                {{ $t("dashboard.subscriptions.price", { price: "5" }) }}
                            </div>
                        </div>
                    </div>

                    <div
                        @click="router.get(route('login'))"
                        class="relative mx-auto max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                    >
                        <div class="flex flex-col items-center gap-4 p-2">
                            <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.top-up.top-up") }}
                            </h3>
                            <div class="flex flex-col items-center">
                                <img src="/credits.png" alt="top-up" />
                                <p class="mt-2 text-sm text-gray-300">{{ $t("dashboard.top-up.get-credits", { credits: "5" }) }}</p>
                                <p class="mt-2 text-sm text-gray-300">{{ $t("dashboard.top-up.expiration") }}</p>
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
