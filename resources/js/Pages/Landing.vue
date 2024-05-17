<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";;
import { ref } from "vue";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay } from "swiper/modules";
import "swiper/css";

import "swiper/css/pagination";
import "swiper/css/navigation";

const calendized = ref<boolean>(false);
</script>

<template>
    <Head>
        <title>Calendize - your calendar's new best friend</title>
        <meta name="description" content="Flights, trains, concerts, conferences, and all other important events that we can't just add to the calendar in one click... So we ignore them. We forget them. We plan multiple events at the same date and time. This sucks, right? That's why Calendize exists - to save your precious times while keeping your calendar neat and updated!" />
    </Head>
    <div class="min-h-screen bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <div
            class="flex w-full flex-col justify-around bg-[url('/home/usage/background.webp')] bg-repeat md:h-full md:min-h-[50%] lg:flex-row"
        >
            <div
                class="flex h-full w-full flex-col justify-center self-center bg-white/85 text-center backdrop-blur-md lg:h-screen lg:w-fit dark:bg-black/85"
            >
                <h1
                    v-show="!calendized"
                    class="m-5 rounded-lg p-6 text-center text-2xl font-semibold uppercase tracking-widest text-gray-800 md:m-12 lg:text-3xl dark:text-gray-200"
                >
                    All these important events <br />
                    you can't just add to your calendar<br />
                    in one click...
                </h1>
                <h2
                    v-show="calendized"
                    class="m-5 rounded-lg p-6 text-center text-2xl font-semibold uppercase tracking-widest text-gray-800 md:m-12 lg:text-3xl dark:text-gray-200"
                >
                    All done! Easy, right?<br />
                    Together, we will keep your calendar<br />
                    neat and tidy!
                </h2>
                <img src="/calendar.png" alt="Calendize" class="mx-auto my-5 size-36 md:mt-10" />
                <h2
                    v-show="!calendized"
                    class="m-5 text-center text-2xl font-semibold uppercase tracking-widest text-gray-800 md:m-12 lg:text-3xl dark:text-gray-200"
                >
                    This sucks, right?
                </h2>
                <Link
                    :href="route('try')"
                    v-show="calendized"
                    class="m-5 w-fit self-center rounded-lg bg-green-700 px-8 py-3 text-xl font-semibold uppercase tracking-widest text-gray-100 hover:bg-green-900 md:m-12"
                >
                    Cool! I'm in!
                </Link>
                <button
                    @click="calendized = !calendized"
                    class="mx-10 mb-5 w-fit self-center rounded-lg px-8 py-3 text-xl font-semibold uppercase tracking-widest text-gray-100 md:mb-12"
                    :class="calendized ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-800 hover:bg-blue-900'"
                >
                    {{ calendized ? "Wait, let's go back..." : "Calendize them!" }}
                </button>
            </div>
            <div class="h-full flex-1 lg:h-screen lg:min-w-[40rem]">
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
                    class="cursor-hand h-full bg-white/85 dark:bg-black/85"
                >
                    <swiper-slide class="flex flex-grow flex-col">
                        <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                            <h2
                                class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                            >
                                Tours
                            </h2>
                            <img
                                class="mx-auto block rounded-xl lg:hidden"
                                :src="calendized ? '/home/usage/tour-calendized-mobile.webp' : '/home/usage/tour-mobile.webp'"
                                alt="tour"
                            />
                            <img
                                class="mx-auto hidden rounded-xl lg:block"
                                :src="calendized ? '/home/usage/tour-calendized.webp' : '/home/usage/tour.webp'"
                                alt="tour"
                            />
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col">
                        <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                            <h2
                                class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                            >
                                Flights
                            </h2>
                            <img
                                class="mx-auto block rounded-xl lg:hidden"
                                :src="calendized ? '/home/usage/flight-calendized-mobile.webp' : '/home/usage/flight-mobile.webp'"
                                alt="flight"
                            />
                            <img
                                class="mx-auto hidden rounded-xl lg:block"
                                :src="calendized ? '/home/usage/flight-calendized.webp' : '/home/usage/flight.webp'"
                                alt="flight"
                            />
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col">
                        <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                            <h2
                                class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                            >
                                Conferences
                            </h2>
                            <img
                                class="mx-auto block rounded-xl lg:hidden"
                                :src="calendized ? '/home/usage/event-calendized-mobile.webp' : '/home/usage/event-mobile.webp'"
                                alt="event"
                            />
                            <img
                                class="mx-auto hidden rounded-xl lg:block"
                                :src="calendized ? '/home/usage/event-calendized.webp' : '/home/usage/event.webp'"
                                alt="event"
                            />
                        </div>
                    </swiper-slide>
                    <swiper-slide class="flex flex-col">
                        <div class="flex h-full flex-col justify-center gap-6 p-5 md:p-12">
                            <h2
                                class="rounded-lg text-center text-xl font-semibold uppercase tracking-widest text-gray-800 lg:p-6 lg:text-3xl dark:text-gray-200"
                            >
                                Concerts
                            </h2>
                            <img
                                class="mx-auto block rounded-xl lg:hidden"
                                :src="calendized ? '/home/usage/concert-calendized-mobile.webp' : '/home/usage/concert-mobile.webp'"
                                alt="concert"
                            />
                            <img
                                class="mx-auto hidden rounded-xl lg:block"
                                :src="calendized ? '/home/usage/concert-calendized.webp' : '/home/usage/concert.webp'"
                                alt="concert"
                            />
                        </div>
                    </swiper-slide>
                </swiper>
            </div>
        </div>
    </div>
</template>
