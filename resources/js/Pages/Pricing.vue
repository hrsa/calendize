<script setup lang="ts">
import { Head, Link, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { ref } from "vue";
import EmailInput from "@/Components/EmailInput.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import EventGenerationTextArea from "@/Components/EventGenerationTextArea.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue"

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
const email = ref<string>("");
const showNotice = ref<boolean>(true);
const calendarEvent = ref<string>("");
const errorMessage = ref<string>("");
const loading = ref<boolean>(false);
const emailIsUnique = ref<boolean>(false);

const sendCalendarEvent = () => {
    loading.value = true;
    window.axios
        .post(route("guest-generate-calendar"), {
            email: email.value,
            calendarEvent: calendarEvent.value,
            timeZone: Intl.DateTimeFormat().resolvedOptions().timeZone ?? null,
        })
        .then(() => {
            router.get(route("verification.notice"));
        });
};

const handleClearError = () => {
    errorMessage.value = "";
    emailIsUnique.value = true;
};

const handleEmailExistsError = () => {
    errorMessage.value = "";
    emailIsUnique.value = false;
};

const handleSomethingWentWrongError = () => {
    errorMessage.value = "Something went wrong... I'll fix it soon!";
    emailIsUnique.value = false;
};
</script>

<template>
    <Head>
        <title>Calendize an event - for free</title>
        <meta
            name="description"
            content="Calendize your event for free by copying and pasting your email content for quick calendar conversion. Register or login for additional features."
        />
    </Head>
    <GuestLayout>
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div
                            v-show="showNotice"
                            @click="showNotice = !showNotice"
                            class="flex cursor-pointer flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <h3 class="mx-auto px-8 text-center text-xl">Welcome!</h3>
                            <h1 class="mx-auto px-8 text-center text-xl">
                                My users can simply forward their emails to me, and I reply with a calendized version!<br />
                                But if you don't want to sign up yet - just <b>copy-paste an email that you want to calendize</b>.
                            </h1>
                            <h3 class="mx-auto px-8 text-center text-sm">
                                You'll have to confirm you email, though - no spammers allowed 😎
                            </h3>
                        </div>
                        <div
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <EventGenerationTextArea v-model="calendarEvent" :loading />
                            <EmailInput
                                class="m-auto"
                                placeholder="email"
                                v-model="email"
                                :class="loading ? 'blur-lg' : ''"
                                @clearError="handleClearError"
                                @somethingWentWrongError="handleSomethingWentWrongError"
                                @emailExistsError="handleEmailExistsError"
                            />
                            <h3
                                v-if="errorMessage"
                                class="mx-auto max-w-sm rounded-xl border-2 border-red-600/50 bg-red-600/50 px-8 py-2 text-center text-white"
                            >
                                {{ errorMessage }}
                            </h3>
                            <div class="m-auto mt-4 flex items-center justify-end">
                                <LoadingSpinner v-if="loading" />

                                <PrimaryButton
                                    v-else
                                    :class="{ 'opacity-25': !emailIsUnique || !calendarEvent }"
                                    :disabled="!emailIsUnique || !calendarEvent"
                                    @click="sendCalendarEvent"
                                >
                                    Calendize and get by email
                                </PrimaryButton>
                            </div>
                            <div
                                class="mx-auto -mb-6 mt-4 justify-between text-center text-sm text-black/50 transition duration-300 dark:text-white/50"
                            >
                                Using Calendize implies that you accept<br />
                                <Link
                                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                                    :href="route('terms-of-service')"
                                    >Terms of service</Link
                                >
                                and
                                <Link
                                    class="font-semibold underline transition duration-300 hover:text-black dark:hover:text-white"
                                    :href="route('privacy-policy')"
                                    >Privacy policy
                                </Link>
                            </div>
                        </div>
                    </div>
    </GuestLayout>
</template>