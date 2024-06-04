<script setup lang="ts">
import ApplicationLogo from "@/Components/ApplicationLogo.vue"
import { Link } from "@inertiajs/vue3"
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900">
        <div>
            <Link :href="route('try')">
                <ApplicationLogo class="size-36 fill-current text-gray-500" />
            </Link>
        </div>

        <div
            class="mt-6 w-full overflow-hidden bg-white px-6 py-4 text-black/50 shadow-md sm:max-w-md sm:rounded-lg dark:bg-gray-800 dark:text-white/50"
        >
            <slot />
        </div>
    </div>
</template>
