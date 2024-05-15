<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {Swiper, SwiperSlide} from "swiper/vue";
import {Navigation} from 'swiper/modules';
import type SwiperClass from 'swiper';

import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import {ref} from "vue";


const activeSlide = ref(1);


const handleProgress = (swiper: SwiperClass) => {
    activeSlide.value = swiper.activeIndex + 1;
}

</script>

<template>
    <Head title="How do i use Calendize?"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">How do i use
                Calendize?</h2>
        </template>

        <div class="text-black/50 dark:text-white/50">
            <div
                class="relative flex min-h-screen flex-col items-center justify-start pt-16 selection:text-white"
            >
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl overflow-hidden rounded-lg bg-white p-6
                            shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300
                            hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3
                            lg:p-10 lg:pb-10 dark:bg-gray-800 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700
                            dark:focus-visible:ring-[#FF2D20]">
                    <h1 class="text-3xl">{{activeSlide}}</h1>
                    <div class="h-full lg:min-w-[40rem] lg:h-full flex-1">
                        <swiper
                            :spaceBetween="0"
                            :centeredSlides="true"
                            :grabCursor="true"
                            :pagination="{ clickable: true }"
                            :autoplay="{
                                          delay: 3500,
                                          disableOnInteraction: false,
                                        }"
                            :modules="[Navigation]"
                            class="h-full mySwiper cursor-hand"
                            ref="swiperEl"
                            @slideChange="handleProgress"
                        >
                            <swiper-slide class="flex flex-col flex-grow">
                                <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                                    <h2 class="rounded-lg lg:p-6 text-center text-xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 lg:text-3xl">
                                        Tours
                                    </h2>
                                    <img class="mx-auto rounded-xl block lg:hidden"
                                         :src="'/home/usage/tour-mobile.webp'"
                                         alt="tour"/>
                                    <img class="mx-auto rounded-xl hidden lg:block"
                                         :src="'/home/usage/tour.webp'"
                                         alt="tour"/>
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                                    <h2 class="rounded-lg lg:p-6 text-center text-xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 lg:text-3xl">
                                        Flights
                                    </h2>
                                    <img class="mx-auto rounded-xl block lg:hidden"
                                         :src="'/home/usage/flight-mobile.webp'"
                                         alt="flight"/>
                                    <img class="mx-auto rounded-xl hidden lg:block"
                                         :src="'/home/usage/flight.webp'"
                                         alt="flight"/>
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                                    <h2 class="rounded-lg lg:p-6 text-center text-xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 lg:text-3xl">
                                        Conferences
                                    </h2>
                                    <img class="mx-auto rounded-xl block lg:hidden"
                                         :src="'/home/usage/event-mobile.webp'"
                                         alt="event"/>
                                    <img class="mx-auto rounded-xl hidden lg:block"
                                         :src="'/home/usage/event.webp'"
                                         alt="event"/>
                                </div>
                            </swiper-slide>
                            <swiper-slide class="flex flex-col">
                                <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                                    <h2 class="rounded-lg lg:p-6 text-center text-xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 lg:text-3xl">
                                        Concerts
                                    </h2>
                                    <img class="mx-auto rounded-xl block lg:hidden"
                                         :src="'/home/usage/concert-mobile.webp'"
                                         alt="concert"/>
                                    <img class="mx-auto rounded-xl hidden lg:block"
                                         :src="'/home/usage/concert.webp'"
                                         alt="concert"/>
                                </div>
                            </swiper-slide>
                        </swiper>
                    </div>


                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
