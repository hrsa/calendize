<script setup lang="ts">
import TextArea from "@/Components/TextArea.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import { trans } from "laravel-vue-i18n"

const model = defineModel<string>({ required: true });

const props = defineProps<{
    loading?: boolean;
}>();

const getPlaceholder = (): string => {
    const placeholderTexts: string[] = [
        trans("event-generation-textarea.appointment"),
        trans("event-generation-textarea.concert"),
        trans("event-generation-textarea.flight"),
        trans("event-generation-textarea.hotel"),
        trans("event-generation-textarea.note"),
        trans("event-generation-textarea.message"),
        trans("event-generation-textarea.reminder"),
    ];
    const index: number = Math.floor(Math.random() * placeholderTexts.length);
    return placeholderTexts[index];
};
</script>

<template>
    <div class="relative h-48 w-full">
        <TextArea
            class="h-48 w-full resize-none text-lg placeholder:text-sm"
            :class="props.loading ? 'blur-lg' : ''"
            :placeholder="getPlaceholder()"
            v-model="model"
        />
        <div v-if="props.loading" role="status" class="absolute left-1/2 top-2/4 -translate-x-1/2 -translate-y-1/2">
            <LoadingSpinner />
        </div>
    </div>
</template>
