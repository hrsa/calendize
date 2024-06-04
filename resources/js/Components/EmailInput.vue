<script setup lang="ts">
import { ref } from "vue"
import axios from "axios"
import type { FormKitNode } from "@formkit/core"

const model = defineModel<string>({ required: true });

const emit = defineEmits<{
    clearError: [];
    emailExistsError: [];
    somethingWentWrongError: [];
}>();

withDefaults(
    defineProps<{
        placeholder?: string | undefined;
        delay?: number | undefined;
        validation?: string | undefined;
        validationVisibility?: "submit" | "blur" | "live" | "dirty" | undefined;
    }>(),
    {
        placeholder: "",
        delay: 1000,
        validation: "required|email",
        validationVisibility: "blur",
    },
);

const input = ref(null);

const checkIfEmailExists = (node: FormKitNode): Promise<boolean> => {
    return axios
        .post(route("users.check-email"), { email: node.value })
        .then(() => {
            emit("clearError");
            return true;
        })
        .catch((error) => {
            if (error.response.status === 403) {
                emit("emailExistsError");
            } else {
                emit("somethingWentWrongError");
            }
            return false;
        });
};
</script>

<template>
    <FormKit
        name="email"
        type="email"
        outer-class="m-auto w-full flex flex-col items-center"
        messages-class="w-sm m-auto"
        message-class="mt-4 mx-auto max-w-sm rounded-xl bg-red-600/50 border-red-600/50 border-2 px-8 py-2 text-center text-white"
        input-class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500
                    dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
        v-model="model"
        :placeholder="placeholder"
        ref="input"
        :delay="delay"
        validation="required|email|(100)checkIfEmailExists"
        :validation-rules="{ checkIfEmailExists }"
        :validation-messages="{
            checkIfEmailExists: 'You already have an account. Please log in to use all my features!',
        }"
        :validation-visibility="validationVisibility"
    />
</template>
