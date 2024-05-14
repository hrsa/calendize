<script setup lang="ts">
import {Head, Link, router} from '@inertiajs/vue3';
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {ref} from "vue";
import EmailInput from "@/Components/EmailInput.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import EventGenerationTextArea from "@/Components/EventGenerationTextArea.vue";


defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
}>();
const email = ref<string>('');
const calendarEvent = ref<string>('');
const errorMessage = ref<string>('');
const loading = ref<boolean>(false);

const emailIsUnique = ref<boolean>(false);

const sendCalendarEvent = () => {
    loading.value = true;
    window.axios.post(route('guest-generate-calendar'),
        {
            "email": email.value,
            "calendarEvent": calendarEvent.value,
            "timeZone": Intl.DateTimeFormat().resolvedOptions().timeZone ?? null
        })
        .then(() => {
            router.get(route('verification.notice'));
        });
}


const handleClearError = () => {
    errorMessage.value = '';
    emailIsUnique.value = true;
}

const handleEmailExistsError = () => {
    errorMessage.value = "";
    emailIsUnique.value = false;
}

const handleSomethingWentWrongError = () => {
    errorMessage.value = "Something went wrong... I'll fix it soon!";
    emailIsUnique.value = false;
}


function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Calendize"/>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 bg-[url('/tile-background-light.png')] dark:bg-[url('/tile-background-black.png')]">
<!--        <img-->
<!--            id="background" alt="background"-->
<!--            class="absolute top-0 -left-20 max-w-[877px]"-->
<!--            src="https://laravel.com/assets/img/welcome/background.svg"-->
<!--        />-->
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white"
        >
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <Link href="/">
                            <ApplicationLogo class="fill-current text-gray-500 size-36"/>
                        </Link>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end gap-6">
                            <Link
                                :href="route('login')"
                                class="bg-gray-50 text-xl rounded-lg font-semibold hover:text-black/70 hover:ring-black/20
                                dark:bg-zinc-900 dark:ring-zinc-600 dark:hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]
                                px-3 py-4 ring-1 ring-white/[0.05] transition duration-300
                                focus:outline-none focus-visible:ring-[#FF2D20]
                                shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] tracking-wide uppercase"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="bg-gray-50 text-xl rounded-lg font-semibold hover:text-black/70 hover:ring-black/20
                                dark:bg-zinc-900 dark:ring-zinc-600 dark:hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]
                                px-3 py-4 ring-1 ring-white/[0.05] transition duration-300
                                focus:outline-none focus-visible:ring-[#FF2D20]
                                shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] tracking-wide uppercase"
                            >
                                Register
                            </Link>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6
                            shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300
                            hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3
                            lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <h3 class="mx-auto px-8 text-center text-xl">
                                Welcome!
                            </h3>
                            <h3 class="mx-auto px-8 text-center text-xl">
                                My users can simply forward their emails to me, and I reply with a calendized version!<br>
                                But if you don't want to sign up yet - just <b>copy-paste an email that you want to
                                calendize</b>.
                            </h3>
                            <h3 class="mx-auto px-8 text-center text-sm">
                                You'll have to confirm you email, though - no spammers allowed 😎
                            </h3>
                        </div>
                        <div
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6
                            shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300
                            hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3
                            lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <EventGenerationTextArea v-model="calendarEvent" :loading/>
                            <EmailInput class="m-auto" placeholder="email" v-model="email"
                                        :class="loading ? 'blur-lg' : ''"
                                        @clearError="handleClearError"
                                        @somethingWentWrongError="handleSomethingWentWrongError"
                                        @emailExistsError="handleEmailExistsError"
                            />
                            <h3
                                v-if="errorMessage"
                                class="mx-auto max-w-sm rounded-xl border-2 border-red-600/50 bg-red-600/50 px-8 py-2 text-center text-white"
                            >{{ errorMessage }}</h3>
                            <div class="m-auto mt-4 flex items-center justify-end">

                                <LoadingSpinner v-if="loading"/>

                                <PrimaryButton v-else :class="{ 'opacity-25': !emailIsUnique || !calendarEvent }"
                                               :disabled="!emailIsUnique || !calendarEvent"
                                               @click="sendCalendarEvent"
                                >
                                    Generate
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
