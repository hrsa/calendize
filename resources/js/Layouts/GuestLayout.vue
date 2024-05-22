<script setup lang="ts">
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="bg-gray-100 bg-[url('/tile-background-light.webp')] text-black/50 dark:bg-black dark:bg-[url('/tile-background-dark.webp')] dark:text-white/50"
    >
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <Link :href="route('try')">
                            <ApplicationLogo class="size-36 fill-current text-gray-500" />
                        </Link>
                    </div>
                    <nav class="-mx-3 flex flex-1 justify-end gap-6">
                        <Link
                            :href="route('pricing')"
                            v-if="!route().current('pricing')"
                            class="rounded-lg bg-gray-50 px-3 py-4 text-sm font-semibold uppercase tracking-wide shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] sm:text-xl dark:bg-zinc-900 dark:ring-zinc-600 dark:hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]"
                        >
                            {{$t('global.buttons.pricing')}}
                        </Link>
                        <Link
                            :href="route('login')"
                            class="rounded-lg bg-gray-50 px-3 py-4 text-sm font-semibold uppercase tracking-wide shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] sm:text-xl hover:bg-green-800 dark:bg-zinc-900 dark:ring-zinc-600 hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]"
                        >
                            {{$t('global.buttons.login-signup')}}
                        </Link>
                    </nav>
                </header>

                <main class="my-6">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
