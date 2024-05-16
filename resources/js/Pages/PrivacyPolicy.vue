<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <Head title="Privacy Policy" />
    <div
        class="bg-gray-100 bg-[url('/tile-background-light.webp')] text-black/50 dark:bg-black dark:bg-[url('/tile-background-dark.webp')] dark:text-white/50"
    >
        <div class="relative flex min-h-screen flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <Link href="/">
                            <ApplicationLogo class="size-36 fill-current text-gray-500" />
                        </Link>
                    </div>
                    <nav class="-mx-3 flex flex-1 flex-wrap justify-end gap-6">
                        <Link
                            :href="route('login')"
                            class="rounded-lg bg-gray-50 px-3 py-4 text-sm font-semibold uppercase tracking-wide shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] sm:text-xl dark:bg-zinc-900 dark:ring-zinc-600 dark:hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]"
                        >
                            Log in
                        </Link>

                        <Link
                            :href="route('register')"
                            class="rounded-lg bg-gray-50 px-3 py-4 text-sm font-semibold uppercase tracking-wide shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] sm:text-xl dark:bg-zinc-900 dark:ring-zinc-600 dark:hover:text-white/80 dark:hover:ring-zinc-300 dark:focus-visible:ring-[#FF2D20]"
                        >
                            Register
                        </Link>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="flex flex-col gap-6 lg:gap-8">
                        <div
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 lg:p-10 lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                        >
                            <div class="container mx-auto px-4 py-8">
                                <h1 class="mb-6 text-center text-3xl font-bold">Privacy policy</h1>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Introduction</h2>
                                    <p>
                                        Welcome to Calendize! We value your privacy and are committed to protecting your personal data. This
                                        Privacy policy explains how we collect, use, share, and protect your information when you use our
                                        Calendize.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Information I collect</h2>
                                    <ul class="list-inside list-disc">
                                        <li>
                                            <strong>Personal Information</strong>: When you sign in through Google or LinkedIn, I save your
                                            name and your email address. Unless I store it, I wouldn't know how to recognize you!
                                        </li>
                                        <li>
                                            <strong>User Content</strong>: Emails and data that you decide to calendize is stored for one
                                            week, and then - deleted automatically.
                                        </li>
                                    </ul>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">How I use the information</h2>
                                    <ul class="list-inside list-disc">
                                        <li><strong>Authentication</strong>: To verify your identity and allow you to securely sign in.</li>
                                        <li>
                                            <strong>Service Provision</strong>: To calendize emails and other data according to your
                                            requests.
                                        </li>
                                        <li><strong>Customer Support</strong>: To respond to your inquiries and provide assistance.</li>
                                    </ul>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Sharing your information</h2>
                                    <p>I do not sell or trade your information. Yes, it's as simple as that!</p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Data security</h2>
                                    <p>
                                        I employ robust security measures to protect your information from unauthorized access or
                                        disclosure. However, please note that no method of internet transmission or electronic storage is
                                        100% secure.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Your choices</h2>
                                    <p>
                                        You can access, update, or delete your personal information by navigating to your account settings.
                                        You can also revoke permissions granted to Google or LinkedIn at any time.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Changes to this policy</h2>
                                    <p>
                                        I may update this privacy policy from time to time. If there are significant changes, I'll notify
                                        you by email.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Contact</h2>
                                    <p>
                                        If you have any questions or concerns about this privacy policy, you can get in touch at
                                        contact@calendize.it.
                                    </p>
                                </section>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
