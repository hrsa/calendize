<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { computed, onMounted, ref } from "vue";
import Modal from "@/Components/Modal.vue";
import { User } from "@/types";
import { useDateFormat } from "@vueuse/shared";
import DangerButton from "@/Components/DangerButton.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { trans } from "laravel-vue-i18n";

interface PaymentConfirmation {
    title: string;
    content: string;
    imageSrc: string;
    imageAlt: string;
}

const props = defineProps<{
    buyCreditsLink: string;
    paymentConfirmation?: PaymentConfirmation | undefined;
}>();

const hover = ref("");
const modalOpen = ref("");
const newSubscription = ref("");
const subscriptionRenewalDate = ref("");
const subscriptionEndDate = ref("");
const paymentMethodUrl = ref("");
const cancelEmail = ref("");

const activeSubscription = (usePage().props.auth.user as User).active_subscription;

const cancelEmailCorrect = computed(() => {
    return cancelEmail.value === (usePage().props.auth.user as User).email;
});

const beginnerSubscriptionText = computed(() => {
    switch (activeSubscription) {
        case "none":
            return trans("dashboard.subscriptions.subscribe");
        case "beginner":
            return trans("dashboard.subscriptions.modify");
        case "classic":
            return trans("dashboard.subscriptions.downgrade");
        case "power":
            return trans("dashboard.subscriptions.downgrade");
        default:
            return trans("dashboard.subscriptions.subscribe");
    }
});

const classicSubscriptionText = computed(() => {
    switch (activeSubscription) {
        case "none":
            return trans("dashboard.subscriptions.subscribe");
        case "beginner":
            return trans("dashboard.subscriptions.upgrade");
        case "classic":
            return trans("dashboard.subscriptions.modify");
        case "power":
            return trans("dashboard.subscriptions.downgrade");
        default:
            return trans("dashboard.subscriptions.subscribe");
    }
});

const powerSubscriptionText = computed(() => {
    switch (activeSubscription) {
        case "none":
            return trans("dashboard.subscriptions.subscribe");
        case "beginner":
            return trans("dashboard.subscriptions.upgrade");
        case "classic":
            return trans("dashboard.subscriptions.upgrade");
        case "power":
            return trans("dashboard.subscriptions.modify");
        default:
            return trans("dashboard.subscriptions.subscribe");
    }
});

const swapAction = computed(() => {
    switch (activeSubscription) {
        case "beginner":
            return trans("dashboard.subscriptions.upgrade");
        case "power":
            return trans("dashboard.subscriptions.downgrade");
        case "classic":
            return newSubscription.value === "beginner"
                ? trans("dashboard.subscriptions.downgrade")
                : trans("dashboard.subscriptions.upgrade");
    }
});

const swapSubscription = (swapDate: string) => {
    window.axios
        .post(route("subscriptions.swap"), {
            newSubscription: newSubscription.value,
            swapDate: swapDate,
        })
        .then(() => {
            modalOpen.value = "";
            router.get(route("dashboard"), {}, { preserveState: false });
        })
        .catch((err) => {
            console.error(err);
        });
};

const cancelSubscription = () => {
    window.axios.post(route("subscriptions.cancel")).then(() => {
        modalOpen.value = "";
        router.get(route("dashboard"));
    });
};

const handleSubscription = (subscriptionName: string) => {
    newSubscription.value = subscriptionName;

    if (activeSubscription === "none") {
        window.axios
            .post(route("subscriptions.subscribe"), {
                type: subscriptionName,
            })
            .then((res) => {
                window.location.href = res.data;
            })
            .catch((err) => {
                console.error(err);
            });
    } else {
        window.axios.get(route("subscriptions.get-modification-data")).then((res) => {
            subscriptionRenewalDate.value = res.data.renewsAt ? useDateFormat(res.data.renewsAt, "HH:mm (DD MMM YYYY)").value : "";
            subscriptionEndDate.value = res.data.endsAt ? useDateFormat(res.data.endsAt, "HH:mm (DD MMM YYYY)").value : "";
            paymentMethodUrl.value = res.data.paymentMethodUrl;
        });

        modalOpen.value = activeSubscription === subscriptionName ? "subscriptionModification" : "subscriptionSwap";
    }
};

onMounted(() => {
    if (props.paymentConfirmation) {
        modalOpen.value = "paymentConfirmation";
    }
});
</script>

<template>
    <Head>
        <title>{{ $t("dashboard.title") }}</title>
        <meta name="description" :content="$t('dashboard.meta')" />
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h1 class="text-center text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">{{ $t("dashboard.title") }}</h1>
        </template>

        <Modal v-if="props.paymentConfirmation" :show="modalOpen === 'paymentConfirmation'" @close="modalOpen = ''">
            <div class="cursor-pointer p-6" @click="modalOpen = ''">
                <img :alt="props.paymentConfirmation?.imageAlt" class="m-auto mb-6 h-36" :src="props.paymentConfirmation?.imageSrc" />
                <h2 class="text-center text-xl font-medium text-gray-900 dark:text-gray-100">
                    {{ props.paymentConfirmation.title }}
                </h2>

                <p class="mt-4 text-center text-lg text-gray-600 dark:text-gray-300">
                    {{ props.paymentConfirmation.content }}
                </p>
            </div>
        </Modal>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm ring-1 ring-white/[0.05] transition duration-300 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] sm:rounded-lg dark:bg-gray-800 dark:ring-zinc-800 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]"
                >
                    <div class="pt-6 text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                        {{ $t("dashboard.subscriptions.title") }}
                    </div>
                    <div class="m-auto flex w-fit flex-wrap items-center gap-6 self-center p-6">
                        <div
                            class="relative mx-auto max-w-48 cursor-pointer rounded-lg border transition duration-300"
                            :class="
                                activeSubscription === 'beginner'
                                    ? 'border-green-600/25 bg-green-600/25 hover:border-green-600/75'
                                    : 'border-gray-100/25 hover:border-gray-100/75'
                            "
                            @mouseenter="hover = 'beginner'"
                            @mouseleave="hover = ''"
                            @click="handleSubscription('beginner')"
                        >
                            <div
                                v-if="hover === 'beginner'"
                                class="absolute flex h-full w-full flex-col content-center items-center justify-center gap-4 rounded-lg bg-white/45 p-2 backdrop-blur-md dark:bg-gray-800/45"
                            >
                                <h3
                                    class="justify-items-center text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100"
                                >
                                    {{ beginnerSubscriptionText }}
                                </h3>
                            </div>
                            <div class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                    {{ $t("dashboard.subscriptions.beginner") }}
                                </h3>
                                <div class="flex flex-col items-center">
                                    <img src="/beginner.png" alt="basic" />
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.credits-per-month", { credits: "10" }) }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.revolving-credits", { credits: "2" }) }}
                                    </p>
                                </div>
                                <div
                                    class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                                >
                                    {{ $t("dashboard.subscriptions.price", { price: "1" }) }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative mx-auto max-w-48 cursor-pointer rounded-lg border transition duration-300"
                            :class="
                                activeSubscription === 'classic'
                                    ? 'border-green-600/25 bg-green-600/25 hover:border-green-600/75'
                                    : 'border-gray-100/25 hover:border-gray-100/75'
                            "
                            @mouseenter="hover = 'classic'"
                            @mouseleave="hover = ''"
                            @click="handleSubscription('classic')"
                        >
                            <div
                                v-if="hover === 'classic'"
                                class="absolute flex h-full w-full flex-col content-center items-center justify-center gap-4 rounded-lg bg-white/45 p-2 backdrop-blur-md dark:bg-gray-800/45"
                            >
                                <h3
                                    class="justify-items-center text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100"
                                >
                                    {{ classicSubscriptionText }}
                                </h3>
                            </div>
                            <div class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                    {{ $t("dashboard.subscriptions.classic") }}
                                </h3>
                                <div class="flex flex-col items-center">
                                    <img src="/classic.png" alt="classic" />
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.credits-per-month", { credits: "25" }) }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.revolving-credits", { credits: "5" }) }}
                                    </p>
                                </div>

                                <div
                                    class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                                >
                                    {{ $t("dashboard.subscriptions.price", { price: "2" }) }}
                                </div>
                            </div>
                        </div>

                        <div
                            class="relative mx-auto max-w-48 cursor-pointer rounded-lg border transition duration-300"
                            :class="
                                activeSubscription === 'power'
                                    ? 'border-green-600/25 bg-green-600/25 hover:border-green-600/75'
                                    : 'border-gray-100/25 hover:border-gray-100/75'
                            "
                            @mouseenter="hover = 'power'"
                            @mouseleave="hover = ''"
                            @click="handleSubscription('power')"
                        >
                            <div
                                v-if="hover === 'power'"
                                class="absolute flex h-full w-full flex-col content-center items-center justify-center gap-4 rounded-lg bg-white/45 p-2 backdrop-blur-md dark:bg-gray-800/45"
                            >
                                <h3
                                    class="justify-items-center text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100"
                                >
                                    {{ powerSubscriptionText }}
                                </h3>
                            </div>
                            <div class="flex flex-col items-center gap-4 p-2">
                                <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                    {{ $t("dashboard.subscriptions.power") }}
                                </h3>
                                <div class="flex flex-col items-center">
                                    <img src="/power.png" alt="power" />
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.credits-per-month", { credits: "100" }) }}
                                    </p>
                                    <p class="mt-2 text-sm text-gray-300">
                                        {{ $t("dashboard.subscriptions.revolving-credits", { credits: "20" }) }}
                                    </p>
                                </div>
                                <div
                                    class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                                >
                                    {{ $t("dashboard.subscriptions.price", { price: "5" }) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <Modal :show="modalOpen === 'subscriptionModification'" @close="modalOpen = ''">
                        <div class="p-6">
                            <h2 class="mb-4 text-center text-xl font-medium uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.subscriptions.current-subscription", { subscription: newSubscription }) }}
                            </h2>
                            <img :alt="newSubscription" class="m-auto mb-6 h-36" :src="`/${newSubscription}.png`" />

                            <template v-if="subscriptionRenewalDate">
                                <p class="text-center text-lg text-gray-600 dark:text-gray-300">
                                    {{ $t("dashboard.subscriptions.renewal-date") }}
                                </p>
                                <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-300">
                                    {{ subscriptionRenewalDate }}
                                </p>
                            </template>

                            <template v-if="subscriptionEndDate">
                                <p class="text-center text-lg text-gray-600 dark:text-gray-300">
                                    {{ $t("dashboard.subscriptions.end-date") }}
                                </p>
                                <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-300">
                                    {{ subscriptionEndDate }}
                                </p>
                            </template>

                            <div class="mt-6 flex justify-around">
                                <a
                                    :href="paymentMethodUrl"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                                    >{{ $t("dashboard.subscriptions.change-payment-method") }}</a
                                >
                                <DangerButton @click="modalOpen = 'subscriptionCancel'">
                                    {{ $t("dashboard.subscriptions.cancel") }}
                                </DangerButton>
                            </div>
                        </div>
                    </Modal>

                    <Modal :show="modalOpen === 'subscriptionSwap'" @close="modalOpen = ''">
                        <div class="p-6">
                            <h2 class="mb-4 text-center text-xl font-medium uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ swapAction }} {{ activeSubscription }} to {{ newSubscription }}
                            </h2>
                            <img :alt="newSubscription" class="m-auto mb-6 h-36" :src="`/${newSubscription}.png`" />

                            <p class="text-center text-lg text-gray-600 dark:text-gray-300">
                                {{
                                    subscriptionRenewalDate
                                        ? $t("dashboard.subscriptions.renewal-date")
                                        : $t("dashboard.subscriptions.end-date")
                                }}
                            </p>
                            <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-300">
                                {{ subscriptionRenewalDate ?? subscriptionEndDate }}
                            </p>

                            <div class="mt-6 flex justify-around">
                                <SecondaryButton @click="swapSubscription('at renewal')"
                                    >{{ swapAction }} {{ $t("dashboard.subscriptions.on-renewal") }}
                                </SecondaryButton>
                                <SecondaryButton @click="swapSubscription('now')" class="!bg-green-600/50 hover:!bg-green-600/75"
                                    >{{ swapAction }} {{ $t("dashboard.subscriptions.now") }}
                                </SecondaryButton>
                            </div>
                        </div>
                    </Modal>

                    <Modal :show="modalOpen === 'subscriptionCancel'" @close="modalOpen = ''">
                        <div class="p-6">
                            <h2 class="mb-4 text-center text-xl font-medium uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                {{ $t("dashboard.cancel-modal.title") }}
                            </h2>
                            <img alt="sad Cally" class="m-auto mb-6 h-36" src="/calendar-sad.png" />

                            <p class="text-center text-lg text-gray-600 dark:text-gray-300">
                                {{ $t("dashboard.cancel-modal.tagline") }}
                            </p>
                            <p class="mt-2 text-center text-lg text-gray-600 dark:text-gray-300">
                                {{
                                    activeSubscription !== "beginner"
                                        ? $t("dashboard.cancel-modal.downgrade-first")
                                        : $t("dashboard.cancel-modal.come-back")
                                }}
                            </p>

                            <div class="mt-4 flex flex-col justify-center gap-2">
                                <p class="text-center text-sm text-gray-600 dark:text-gray-300">
                                    {{ $t("dashboard.cancel-modal.fill-in-email") }}
                                </p>
                                <TextInput class="m-auto mt-2" v-model="cancelEmail" />
                            </div>

                            <div class="mt-6 flex justify-around">
                                <button
                                    v-if="activeSubscription !== 'beginner'"
                                    @click="modalOpen = ''"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-sm transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-500 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:focus:ring-offset-gray-800"
                                >
                                    {{ $t("dashboard.cancel-modal.lets-downgrade") }}
                                </button>
                                <DangerButton :disabled="!cancelEmailCorrect" @click="cancelSubscription" class="disabled:opacity-25"
                                    >{{ $t("dashboard.subscriptions.cancel") }}
                                </DangerButton>
                            </div>
                        </div>
                    </Modal>

                    <div class="pt-6 text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                        {{ $t("dashboard.top-up.title") }}
                    </div>
                    <div class="m-auto flex w-fit items-center gap-6 p-6">
                        <a
                            :href="props.buyCreditsLink"
                            class="relative max-w-48 cursor-pointer rounded-lg border border-gray-100/25 transition duration-300 hover:border-gray-100/75"
                            @mouseenter="hover = 'credits'"
                            @mouseleave="hover = ''"
                        >
                            <div
                                v-if="hover === 'credits'"
                                class="absolute flex h-full w-full flex-col content-center items-center justify-center gap-4 rounded-lg bg-white/45 p-2 backdrop-blur-md dark:bg-gray-800/45"
                            >
                                <h3
                                    class="justify-items-center text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100"
                                >
                                    {{ $t("dashboard.top-up.buy-credits") }}
                                </h3>
                            </div>
                            <div class="flex flex-col items-center gap-4 px-4 py-2">
                                <h3 class="text-center text-lg uppercase tracking-widest text-gray-900 dark:text-gray-100">
                                    {{ $t("dashboard.top-up.top-up") }}
                                </h3>
                                <div class="flex flex-col items-center">
                                    <img src="/credits.png" alt="top-up" />
                                    <p class="mt-2 text-sm text-gray-300">{{ $t("dashboard.top-up.get-credits", { credits: "5" }) }}</p>
                                    <p class="mt-2 text-sm text-gray-300">{{ $t("dashboard.top-up.expiration") }}</p>
                                </div>
                                <div
                                    class="inline-flex w-full justify-center px-4 py-2 text-2xl font-semibold uppercase text-gray-700 transition duration-150 ease-in-out dark:text-gray-300"
                                >
                                    1 €
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
