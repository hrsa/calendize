<script setup lang="ts">
import {onMounted, ref} from 'vue';
import {watchDebounced} from "@vueuse/core";
import axios from "axios";

const model = defineModel<string>({required: true});

const emit = defineEmits<{
  clearError
  hasError: [value: string]
}>();

withDefaults(defineProps<{
    placeholder?: string;
    delay?: number;
    validation?: string;
    validationVisibility?: string;
}>(), {
    placeholder: null,
    delay: 1000,
    validation: "required|email",
    validationVisibility: "blur"
});

const input = ref(null);

const checkIfEmailExists = (node) => {
    axios.post(route('user.check-email'),{ "email":node.value }).then((res) => {
        emit('clearError')
        return true;
    }).catch((error) => {
       if (error.response.status === 403) {
           emit('hasError', "You have already used your free credit. Please sign up to get more!");
       } else {
           emit('hasError', "Something went wrong... I'll fix it soon!");
       }
       return false;
    });
}

// onMounted(() => {
//     if (input.value?.hasAttribute('autofocus')) {
//         input.value?.focus();
//     }
// });

defineExpose({focus: () => input.value?.focus()});
</script>

<template>
    <FormKit
        name="email"
        type="email"
        outer-class="m-auto w-full flex flex-col items-center"
        messages-class="w-sm m-auto"
        message-class="mt-4 mx-auto max-w-sm rounded-xl bg-red-600/50 border-red-600/50 border-2 px-8 py-2 text-center text-white"
        input-class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        v-model="model"
        :placeholder="placeholder"
        ref="input"
        :delay=delay
        validation="required|email|(100)checkIfEmailExists"
        :validation-rules="{ checkIfEmailExists }"
        :validation-visibility=validationVisibility
    />
</template>
