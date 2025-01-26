<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue"
import { Head, router } from "@inertiajs/vue3"
import { Swiper, SwiperSlide } from "swiper/vue"
import { Navigation } from "swiper/modules"
import type SwiperClass from "swiper"

import "swiper/css"
import "swiper/css/pagination"
import "swiper/css/navigation"
import { ref } from "vue"
import PrimaryButton from "@/Components/PrimaryButton.vue"

interface SwiperElement extends HTMLElement {
    swiper: any;
}

const activeSlide = ref(1);

const handleProgress = (swiper: SwiperClass) => {
    activeSlide.value = swiper.activeIndex + 1;
};

const setSlide = (num: number) => {
    const swiperEl = (document.querySelector(".swiper") as SwiperElement)?.swiper;
    if (swiperEl) {
        swiperEl.slideTo(num - 1);
    }
};
</script>

<template>
    <Head>
        <title>{{ $t("how-to-use.title") }}</title>
        <meta name="description" :content="$t('how-to-use.meta')" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $t("how-to-use.title") }}</h1>
        </template>

        <div class="text-black/50 dark:text-white/50">
            <div class="relative flex min-h-screen flex-col items-center justify-start pt-16 selection:text-white">
                <div
                    class="relative w-full max-w-2xl overflow-hidden rounded-lg bg-white p-6 px-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05]
                        transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-hidden focus-visible:ring-[#FF2D20] md:row-span-3 lg:max-w-7xl
                        lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                >
                    <div class="flex w-full justify-around">
                        <div
                            @click="setSlide(1)"
                            class="flex size-8 cursor-pointer items-center justify-center rounded-full md:size-16"
                            :class="activeSlide >= 1 ? 'bg-green-700 text-white' : 'bg-gray-200'"
                        >
                            <p class="p-4 text-lg md:p-8 md:text-2xl">1</p>
                        </div>
                        <hr
                            class="h-1 w-full place-self-center border-0"
                            :class="activeSlide >= 2 ? 'bg-green-700' : 'bg-white dark:bg-gray-800'"
                        />
                        <div
                            @click="setSlide(2)"
                            class="flex size-8 cursor-pointer items-center justify-center rounded-full md:size-16"
                            :class="activeSlide >= 2 ? 'bg-green-700 text-white' : 'bg-gray-200 dark:bg-gray-500'"
                        >
                            <p class="p-4 text-lg md:p-8 md:text-2xl">2</p>
                        </div>
                        <hr
                            class="h-1 w-full place-self-center border-0"
                            :class="activeSlide >= 3 ? 'bg-green-700' : 'bg-white dark:bg-gray-800'"
                        />
                        <div
                            @click="setSlide(3)"
                            class="flex size-8 cursor-pointer items-center justify-center rounded-full md:size-16"
                            :class="activeSlide >= 3 ? 'bg-green-700 text-white' : 'bg-gray-200 dark:bg-gray-500'"
                        >
                            <p class="p-4 text-lg md:p-8 md:text-2xl">3</p>
                        </div>
                        <hr
                            class="h-1 w-full place-self-center border-0"
                            :class="activeSlide >= 4 ? 'bg-green-700' : 'bg-white dark:bg-gray-800'"
                        />
                        <div
                            @click="setSlide(4)"
                            class="flex size-8 cursor-pointer items-center justify-center rounded-full md:size-16"
                            :class="activeSlide >= 4 ? 'bg-green-700 text-white' : 'bg-gray-200 dark:bg-gray-500'"
                        >
                            <p class="p-4 text-lg md:p-8 md:text-2xl">4</p>
                        </div>
                    </div>
                    <div class="h-full flex-1 lg:h-full lg:min-w-[40rem]">
                        <swiper
                            :spaceBetween="0"
                            :centeredSlides="true"
                            :grabCursor="true"
                            :modules="[Navigation]"
                            class="how-to-use-swiper cursor-hand h-full"
                            @slideChange="handleProgress"
                        >
                            <swiper-slide class="flex grow flex-col">
                                <div class="flex h-full flex-col justify-center gap-4 p-5 md:p-12">
                                    <h2
                                        class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                                    >
                                        {{ $t("how-to-use.find-an-event") }}
                                    </h2>
                                    <img class="mx-auto block rounded-xl lg:hidden" :src="'/home/usage/tour-mobile.webp'" alt="tour" />
                                    <img class="mx-auto hidden rounded-xl lg:block" :src="'/home/usage/tour.webp'" alt="tour" />
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-4 p-5 md:p-12">
                                    <h2
                                        class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                                    >
                                        {{ $t("how-to-use.send-to-me") }}
                                    </h2>
                                    <div class="flex flex-col">
                                        <div
                                            class="rounded-lg text-center text-base font-semibold uppercase tracking-widest text-gray-800 lg:text-xl dark:text-gray-200"
                                        >
                                            {{ $t("how-to-use.forward-email", { email: "hey@calendize.it" }) }}
                                        </div>
                                        <div
                                            class="rounded-lg text-center text-lg font-extrabold uppercase tracking-widest text-gray-900 lg:text-2xl dark:text-gray-400"
                                        >
                                            {{ $t("how-to-use.or") }}
                                        </div>
                                        <div
                                            class="rounded-lg text-center text-base font-semibold uppercase tracking-widest text-gray-800 lg:text-xl dark:text-gray-200"
                                        >
                                            <span class="align-middle">{{ $t("how-to-use.copy-paste") }}</span>
                                            <PrimaryButton @click="router.get(route('generate'))" class="m-auto w-fit">
                                                {{ $t("how-to-use.button") }}
                                            </PrimaryButton>
                                        </div>
                                    </div>
                                    <img
                                        class="mx-auto block rounded-xl lg:hidden"
                                        :src="'/home/usage/tour-forward-mobile.webp'"
                                        alt="tour"
                                    />
                                    <img class="mx-auto hidden rounded-xl lg:block" :src="'/home/usage/tour-forward.webp'" alt="tour" />
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-4 p-5 md:p-12">
                                    <h2
                                        class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                                    >
                                        {{ $t("how-to-use.check-email") }}
                                    </h2>
                                    <div
                                        class="rounded-lg text-center text-base font-semibold uppercase tracking-widest text-gray-800 lg:text-xl dark:text-gray-200"
                                    >
                                        {{ $t("how-to-use.wait-for-it") }}
                                    </div>
                                    <img
                                        class="mx-auto block rounded-xl lg:hidden"
                                        :src="'/home/usage/tour-calendized-mobile.webp'"
                                        alt="tour"
                                    />
                                    <img
                                        class="mx-auto hidden rounded-xl lg:block"
                                        :src="'/home/usage/tour-calendized-gotmail.webp'"
                                        alt="tour"
                                    />
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-4 p-5 md:p-12">
                                    <h2
                                        class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                                    >
                                        {{ $t("how-to-use.add-to-calendar") }}
                                    </h2>
                                    <img
                                        class="mx-auto block rounded-xl lg:hidden"
                                        :src="'/home/usage/tour-calendized-adding-mobile.webp'"
                                        alt="tour"
                                    />
                                    <img class="mx-auto hidden rounded-xl lg:block" :src="'/home/usage/tour-calendized.webp'" alt="tour" />
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
