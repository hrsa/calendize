<script setup lang="ts">
import {Link} from "@inertiajs/vue3"
withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: 'mx-auto -mb-6 mt-4 justify-between text-center text-sm text-black/50 transition duration-300 dark:text-white/50',
    }
);
</script>

<template>
    <div :class>
                Using Calendize implies that you accept<br />
                <Link
                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                    :href="route('terms-of-service')"
                    >Terms of service</Link
                > and
                <Link
                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                    :href="route('privacy-policy')"
                    >Privacy policy
                </Link>
            </div>
</template>
