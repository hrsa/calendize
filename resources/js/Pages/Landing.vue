<script setup lang="ts">
import {Head} from '@inertiajs/vue3';
import {ref} from "vue";
import {Swiper, SwiperSlide} from "swiper/vue";
import {Autoplay} from 'swiper/modules';
import 'swiper/css';

import 'swiper/css/pagination';
import 'swiper/css/navigation';


defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
}>();
const email = ref<string>('');
const calendarEvent = ref<string>('');
const errorMessage = ref<string>('');
const loading = ref<boolean>(false);
const emailIsUnique = ref<boolean>(false);

</script>

<template>
    <Head title="Calendize"/>
    <div class="min-h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="flex flex-col md:flex-row md:h-screen w-full justify-around bg-[url('/home/usage/background.webp')] bg-repeat">
            <div
                class="self-center text-center bg-black/85 backdrop-blur-md h-full justify-center flex flex-col order-last md:order-first">
                <h2 class="m-12 text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 rounded-lg p-6">
                    All these important events <br>
                    you can't just add to your calendar<br>
                    in one click...
                </h2>
                <img src="/calendar.png" class="mx-auto mt-10 size-36"/>
                <h2 class="m-12 text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200">
                    This sucks, right?
                </h2>
                <button
                    class="w-fit mx-auto px-8 py-3 rounded-lg uppercase tracking-widest font-semibold text-xl bg-blue-800 hover:bg-blue-900 text-gray-100 mb-12">
                    Let me help!
                </button>

            </div>
            <div class="h-full min-w-0 flex-1">
                <swiper
                    :spaceBetween="0"
                    :centeredSlides="true"
                    :grabCursor="true"
                    :loop="true"
                    :autoplay="{
              delay: 3500,
              disableOnInteraction: false,
            }"
                    :modules="[Autoplay]"
                    class="h-full mySwiper cursor-hand"
                >
                    <swiper-slide class="flex flex-col bg-black/85">
                        <div class="flex h-full flex-col justify-center gap-6 p-12">
                            <img class="mx-auto rounded-xl" src="/home/usage/169/_tour.webp"/>
                            <h2 class="text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 rounded-lg p-6">
                                Tours
                            </h2>
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col bg-black/85">
                        <div class="flex h-full flex-col justify-center gap-6 p-12">
                            <img class="mx-auto rounded-xl" src="/home/usage/169/_flight.webp"/>
                            <h2 class="text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 rounded-lg p-6">
                                Flights
                            </h2>
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col bg-black/85">
                        <div class="flex h-full flex-col justify-center gap-6 p-12">
                            <img class="mx-auto rounded-xl" src="/home/usage/169/_event.webp"/>
                            <h2 class="text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 rounded-lg p-6">
                                Conferences
                            </h2>
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col bg-black/85">
                        <div class="flex h-full flex-col justify-center gap-6 p-12">
                            <img class="mx-auto rounded-xl" src="/home/usage/169/_concert.webp"/>
                            <h2 class="text-center text-3xl font-semibold uppercase tracking-widest text-gray-800 dark:text-gray-200 rounded-lg p-6">
                                Concerts
                            </h2>
                        </div>
                    </swiper-slide>
                </swiper>
            </div>
        </div>

    </div>
</template>
