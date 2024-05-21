<script setup lang="ts">
import Checkbox from "@/Components/Checkbox.vue";
import AuthDialogLayout from "@/Layouts/AuthDialogLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { watchDebounced } from "@vueuse/core";
import { ref } from "vue";
import LegalFooter from "@/Components/LegalFooter.vue"

defineProps<{
    canResetPassword?: boolean;
    status?: string;
}>();

const emailExists = ref(false);
const emailRef = ref("");

const form = useForm({
    name: "",
    email: "",
    password: "",
    remember: false,
});

const login = () => {
    form.email = emailRef.value;
    form.post(route("login"), {
        onFinish: () => {
            form.reset("password");
        },
    });
};

const register = () => {
    form.email = emailRef.value;
    form.post(route("register"), {
        onFinish: () => {
            form.reset("password");
        },
    });
};

watchDebounced(
    emailRef,
    (newValue) => {
            window.axios
                .post(route("user.check-email"), { email: newValue })
                .then(() => {
                    emailExists.value = false
                })
                .catch((error) => {
                    if (error.response.status === 403) {
                        emailExists.value = true
                    } else {
                        console.log(error.response.data.error)
                    }
                })
    },
    { debounce: 100, maxWait: 500 },
);
</script>

<template>
    <AuthDialogLayout>
        <Head>
            <title>Log in / Register</title>
        <meta name="description"
              content="An email, name and password - that's all you need to keep calendar neat and tidy! Create a new account and get 5 credits for free." />
    </Head>
        <h1 class="mx-auto px-8 text-center text-xl">{{!emailRef ? 'Login / Register' : emailExists ? 'Login' : 'Register'}}</h1>
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

        <form @submit.prevent>
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="emailRef"
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

            <div class="mt-4" v-if="!emailExists">
                <InputLabel for="name" value="Name" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div v-if="emailExists" class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div v-if="emailExists" class="mt-4 flex items-center justify-between">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton  @click="login" class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
            <div v-if="!emailExists" class="mt-8 flex items-center justify-center">
                <PrimaryButton @click="register" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>

            <LegalFooter class="mt-4 justify-between text-center text-sm text-black/50 transition duration-300 dark:text-white/50" />
        </form>
    </AuthDialogLayout>
</template>
