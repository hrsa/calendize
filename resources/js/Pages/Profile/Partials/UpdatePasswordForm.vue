<script setup lang="ts">
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";

withDefaults(
    defineProps<{
        titleLabel: string;
        descriptionLabel: string;
        buttonLabel: string;
        newPasswordLabel: string;
        successMessage: string | undefined;
    }>(),
    {
        titleLabel: "Update Password",
        descriptionLabel: "Ensure your account is using a long, random password to stay secure.",
        newPasswordLabel: "New password",
        buttonLabel: "Update Password",
        successMessage: "Password was updated!",
    },
);

const passwordInput = ref<HTMLInputElement | null>(null);
const emit = defineEmits(["password-set"]);

const form = useForm({
    password: "",
});

const updatePassword = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => {
            emit("password-set");
            form.reset();
        },
        onError: () => {
            if (form.errors.password) {
                form.reset("password");
                passwordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section>
        <header class="text-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ titleLabel }}</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ descriptionLabel }}
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
            <div>
                <InputLabel for="password" :value="newPasswordLabel" />

                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />

                <InputError :message="form.errors.password" class="mt-2" />
            </div>

            <div class="flex flex-col items-center justify-center gap-4">
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-green-700 dark:text-green-600">
                        {{ successMessage }}
                    </p>
                </Transition>

                <PrimaryButton :disabled="form.processing">{{ buttonLabel }}</PrimaryButton>
            </div>
        </form>
    </section>
</template>
