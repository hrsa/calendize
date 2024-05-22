<script setup lang="ts">
import { Link } from "@inertiajs/vue3";

withDefaults(
    defineProps<{
        class?: string;
    }>(),
    {
        class: "mx-auto -mb-6 mt-4 justify-between text-center text-sm text-black/50 transition duration-300 dark:text-white/50",
    },
);
</script>

<template>
    <div :class>
        {{ $t("legal-footer.main") }}<br />
        <Link
            class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
            :href="route('terms-of-service')"
            >{{ $t("legal-footer.terms-of-service") }}
        </Link>
        {{ $t("legal-footer.and") }}
        <Link class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white" :href="route('privacy-policy')"
            >{{ $t("legal-footer.privacy-policy") }}
        </Link>
    </div>
</template>
