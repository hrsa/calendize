<script setup lang="ts">
import { Head, Link } from "@inertiajs/vue3";
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
</script>

<template>
    <Head>
        <title>Terms of Service</title>
        <meta
            name="description"
            content="Read the Terms of Service for Calendize, explaining user accounts, acceptable use, email communication, data privacy, intellectual property, subscriptions, termination, disclaimers, limitation of liability, governing law, and change policies."
        />
    </Head>
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
                                <h1 class="mb-6 text-center text-3xl font-bold">Terms of service</h1>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Introduction</h2>
                                    <p>
                                        By using Calendize, you agree to these terms of service. These terms may be updated from time to
                                        time, and I'll do my best to keep you informed, but please check back regularly.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Email communication</h2>
                                    <p>
                                        By using Calendize, you agree to receive transactional emails from me. These emails include account
                                        confirmation, calendized events, password reset links, and other necessary communications related to
                                        your use of Calendize. I won't send you any promotional emails or newsletters.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">User accounts</h2>
                                    <p>
                                        To use Calendize, you must create an account and provide accurate and up-to-date information. You
                                        are responsible for maintaining the confidentiality of your account details and for any activity
                                        that occurs under your account.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Acceptable use</h2>
                                    <p>
                                        You are responsible for the data you upload and transform using Calendize. Please do not generate
                                        malicious or harmful content! If the resulting file is unusable, we will refund the credits used.
                                        However, repeated errors may lead to credits being deducted as outlined in section 4.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Data and privacy</h2>
                                    <p>
                                        All user data is subject to my
                                        <Link class="font-semibold underline" :href="route('privacy-policy')">Privacy policy</Link>. The
                                        accuracy of the calendized events depends on the data you provide. I do our best to generate
                                        accurate and detailed files though - my goal is to be useful to you!
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Intellectual property</h2>
                                    <p>
                                        Users maintain all intellectual property rights to the content they generate through Calendize. You
                                        may use and modify your calendized events as you see fit.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Subscriptions and credits</h2>
                                    <p>
                                        Users may subscribe to different plans or purchase credits in bulk. Subscription plans increase your
                                        credit balance monthly, with 20% rollover credits transferred in the end of the month. Bulk credits
                                        stay in your account indefinitely. Upon termination, I cannot reimburse charges for partially used
                                        subscriptions or partially used bulk credits.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Termination</h2>
                                    <p>
                                        We reserve the right to terminate your account if you don't abide by these Terms of use and the
                                        <Link class="font-semibold underline" :href="route('privacy-policy')">Privacy policy</Link>. Upon
                                        termination, you will lose access to your account and any remaining credits. Refunds will be issued
                                        for subscriptions or bulk credits <span class="font-extrabold">only if</span>
                                        they haven't been partially used.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Disclaimers</h2>
                                    <p>
                                        Calendize is provided "as is" and without warranties of any kind. I do my best, but I cannot
                                        guarantee the absolute accuracy or completeness of any calendized event.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Limitation of liability</h2>
                                    <p>
                                        In no event shall Calendize be liable for any indirect, incidental, special, consequential, or
                                        punitive damages arising out of or related to your use of the service.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Governing law</h2>
                                    <p>
                                        These Terms of Service are governed by the laws of France. Any disputes arising from these terms or
                                        your use of Calendize shall be resolved in the French courts.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Changes to the Terms</h2>
                                    <p>
                                        We reserve the right to update or modify these terms. Your continued use of Calendize signifies your
                                        acceptance of any changes.
                                    </p>
                                </section>

                                <section class="mb-6">
                                    <h2 class="mb-4 text-2xl font-semibold">Contact Information</h2>
                                    <p>
                                        If you have any questions or concerns about these Terms of Service, please contact us at
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
