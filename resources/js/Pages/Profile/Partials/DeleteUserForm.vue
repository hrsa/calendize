<script setup lang="ts">
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route("profile.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="text-center">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $t("delete-account.title") }}</h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ $t("delete-account.description") }}
            </p>
        </header>

        <div class="flex justify-center">
            <DangerButton @click="confirmUserDeletion">{{ $t("delete-account.button") }}</DangerButton>
        </div>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <img alt="Calendize logo" class="m-auto mb-6 size-24" src="/calendar-sad.png" />
                <h2 class="text-center text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ $t("delete-account.modal.title") }}
                </h2>

                <p v-html="$t('delete-account.modal.description')" class="mt-1 text-center text-sm text-gray-600 dark:text-gray-400" />

                <p v-html="$t('delete-account.modal.enter-password')" class="mt-1 text-center text-sm text-gray-600 dark:text-gray-400" />

                <div class="mt-6">
                    <InputLabel for="password" value="Password" class="sr-only" />

                    <TextInput
                        id="password"
                        ref="passwordInput"
                        v-model="form.password"
                        type="password"
                        class="mx-auto mt-1 block w-3/4"
                        :placeholder="$t('delete-account.password')"
                        @keyup.enter="deleteUser"
                    />

                    <InputError :message="form.errors.password" class="mx-auto mt-2 flex justify-center" />
                </div>

                <div class="mt-6 flex justify-center">
                    <SecondaryButton @click="closeModal"> {{ $t('delete-account.cancel') }} </SecondaryButton>

                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deleteUser">
                        {{ $t('delete-account.button') }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
