<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head} from '@inertiajs/vue3';
import {onMounted, ref} from "vue";
import Modal from "@/Components/Modal.vue";

interface PaymentConfirmation {
    title: string;
    content: string;
    imageSrc: string;
    imageAlt: string;
}

const props = defineProps<{
    activeSubscription?: string | undefined;
    buyCreditsLink: string;
    paymentConfirmation?: PaymentConfirmation | undefined;
}>();

const hover = ref('');
const paymentConfirmationOpen = ref(false);

onMounted(() => {
    if (props.paymentConfirmation) {
        paymentConfirmationOpen.value = true;
    }
})
</script>

<template>
    <Head title="Dashboard"/>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Dashboard</h2>
        </template>

        <Modal :show="paymentConfirmationOpen" @close="paymentConfirmationOpen = false">
            <div class="p-6">
                <img :alt="props.paymentConfirmation?.imageAlt" class="m-auto mb-6 size-36"
                     :src="props.paymentConfirmation?.imageSrc"/>
                <h2 class="text-xl font-medium text-center text-gray-900 dark:text-gray-100">
                    {{ props.paymentConfirmation.title }}
                </h2>

                <p class="mt-4 text-lg text-center text-gray-600 dark:text-gray-300">
                    {{ props.paymentConfirmation.content }}
                </p>
            </div>
        </Modal>


        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                    <div class="pt-6 text-lg text-gray-900 text-center dark:text-gray-100">My subscriptions</div>
                    <div class="m-auto flex w-fit items-center gap-6 p-6">
                        <div
                            class="rounded-lg border relative cursor-pointer transition duration-300 max-w-48"
                            :class="props.activeSubscription === 'beginner'
                            ? 'border-green-600/25 hover:border-green-600/75 bg-green-600/25'
                            : 'border-gray-100/25 hover:border-gray-100/75'"
                            @mouseenter="hover='beginner'"
                            @mouseleave="hover=''"
                        >
                            <div v-if="hover==='beginner'"
                                 class="flex flex-col items-center gap-4 p-2 absolute w-full h-full justify-center content-center bg-gray-800/45 backdrop-blur-md rounded-lg">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest justify-items-center dark:text-gray-100">
                                    Modify</h3>
                            </div>
                            <div
                                class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest dark:text-gray-100">
                                    Beginner</h3>
                                <div class="flex flex-col items-center">
                                    <img src="/beginner.png" alt="basic"/>
                                    <p class="mt-2 text-sm text-gray-300">10 credits / month</p>
                                </div>
                                <div class="inline-flex justify-center px-4 py-2 text-2xl
                                font-semibold text-gray-700 dark:text-gray-300 uppercase
                                transition ease-in-out duration-150 w-full"
                                >1€
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-lg border relative cursor-pointer transition duration-300 max-w-48"
                            :class="props.activeSubscription === 'classic'
                            ? 'border-green-600/25 hover:border-green-600/75 bg-green-600/25'
                            : 'border-gray-100/25 hover:border-gray-100/75'"
                            @mouseenter="hover='classic'"
                            @mouseleave="hover=''"
                        >
                            <div v-if="hover==='classic'"
                                 class="flex flex-col items-center gap-4 p-2 absolute w-full h-full justify-center content-center bg-gray-800/45 backdrop-blur-md rounded-lg">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest justify-items-center dark:text-gray-100">
                                    Modify</h3>
                            </div>
                            <div
                                class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest dark:text-gray-100">
                                    Classic</h3>
                                <div class="flex flex-col items-center">
                                    <img src="/classic.png" alt="classic"/>
                                    <p class="mt-2 text-sm text-gray-300">25 credits / month</p>
                                </div>

                                <div class="inline-flex justify-center px-4 py-2 text-2xl
                                font-semibold text-gray-700 dark:text-gray-300 uppercase
                                transition ease-in-out duration-150 w-full"
                                >2€
                                </div>
                            </div>
                        </div>

                        <div
                            class="rounded-lg border relative cursor-pointer transition duration-300 max-w-48"
                            :class="props.activeSubscription === 'power'
                            ? 'border-green-600/25 hover:border-green-600/75 bg-green-600/25'
                            : 'border-gray-100/25 hover:border-gray-100/75'"
                            @mouseenter="hover='power'"
                            @mouseleave="hover=''"
                        >
                            <div v-if="hover==='power'"
                                 class="flex flex-col items-center gap-4 p-2 absolute w-full h-full justify-center content-center bg-gray-800/45 backdrop-blur-md rounded-lg">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest justify-items-center dark:text-gray-100">
                                    Modify</h3>
                            </div>
                            <div
                                class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest dark:text-gray-100">
                                    Power</h3>
                                <div class="flex flex-col items-center">
                                    <img src="/power.png" alt="power"/>
                                    <p class="mt-2 text-sm text-gray-300">100 credits / month</p>
                                </div>
                                <div class="inline-flex justify-center px-4 py-2 text-2xl
                                font-semibold text-gray-700 dark:text-gray-300 uppercase
                                transition ease-in-out duration-150 w-full"
                                >
                                    5€
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 text-lg text-gray-900 text-center dark:text-gray-100">Top up credits</div>
                    <div class="m-auto flex w-fit items-center gap-6 p-6">
                        <a
                            class="rounded-lg border cursor-pointer relative border-gray-100/25 cursor-pointer transition duration-300 max-w-48 hover:border-gray-100/75"
                            @mouseenter="hover='credits'"
                            @mouseleave="hover=''"
                            :href="props.buyCreditsLink"
                        >
                            <div v-if="hover==='credits'"
                                 class="flex flex-col items-center gap-4 p-2 absolute w-full h-full justify-center content-center bg-gray-800/45 backdrop-blur-md rounded-lg">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest justify-items-center dark:text-gray-100">
                                    Buy credits</h3>
                            </div>
                            <div
                                class="flex flex-col items-center gap-4 py-2 px-4">
                                <h3 class="text-lg text-gray-900 uppercase text-center tracking-widest dark:text-gray-100">
                                    Top up</h3>
                                <div class="flex flex-col items-center">
                                    <img src="/credits.png" alt="top-up"/>
                                    <p class="mt-2 text-sm text-gray-300">Get 5 credits</p>
                                </div>
                                <div class="inline-flex justify-center px-4 py-2 text-2xl
                                font-semibold text-gray-700 dark:text-gray-300 uppercase
                                transition ease-in-out duration-150 w-full"
                                >1€
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
