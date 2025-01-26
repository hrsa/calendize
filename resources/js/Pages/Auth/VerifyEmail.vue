<script setup lang="ts">
import { computed } from 'vue';
import AuthDialogLayout from '@/Layouts/AuthDialogLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent');
</script>

<template>
    <AuthDialogLayout>
        <Head title="Email Verification" />

        <div class="mb-4 text-lg font-semibold text-center text-gray-600 dark:text-gray-400">
            Thanks for your interest in Calendize!
        </div>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Before I send you the calendized event, <b>we need to confirm that you're a real human</b>.
            I know, it's a hassle - but there are so many bots nowadays...
        </div>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Could you please verify your email address by clicking on the link I've just emailed to you?
            If you didn't get it, feel free to get another.
        </div>

        <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400" v-if="verificationLinkSent">
            All done, the verification link has been sent to your email!
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Resend Verification Email
                </PrimaryButton>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-hidden focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >Log Out</Link
                >
            </div>
        </form>
    </AuthDialogLayout>
</template>
