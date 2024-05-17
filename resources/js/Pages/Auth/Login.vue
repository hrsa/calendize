<script setup lang="ts">
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => {
            form.reset("password");
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="my-6 flex place-items-center justify-center gap-6">
            <div class="cursor-pointer" @click="router.get(route('socialite.google.redirect'))">
                <img class="size-10" src="/social/google.svg" alt="google" />
            </div>
            <div class="cursor-pointer" @click="router.get(route('socialite.linkedin.redirect'))">
                <img class="size-10" src="/social/linkedin.svg" alt="linkedin" />
            </div>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="mt-4 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"> Log in </PrimaryButton>
            </div>

            <div class="my-4 justify-between text-center text-sm text-black/50 transition duration-300 dark:text-white/50">
                Using Calendize implies that you accept my<br />
                <Link
                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                    :href="route('terms-of-service')"
                    >Terms of service
                </Link>
                and
                <Link
                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                    :href="route('privacy-policy')"
                    >Privacy policy
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
