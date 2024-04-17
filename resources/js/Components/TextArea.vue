<script setup lang="ts">
import { onMounted, ref } from 'vue';

const model = defineModel<string>({ required: true });

const props = defineProps<{
    placeholder?: string;
}>();

const textarea = ref<HTMLTextAreaElement | null>(null);

onMounted(() => {
    if (textarea.value?.hasAttribute('autofocus')) {
        textarea.value?.focus();
    }
});

defineExpose({ focus: () => textarea.value?.focus() });
</script>

<template>
    <textarea
        class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        v-model="model"
        :placeholder="props.placeholder"
        ref="textarea"
    />
</template>
