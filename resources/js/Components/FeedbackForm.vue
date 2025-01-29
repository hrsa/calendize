<script setup lang="ts">
import { ref, defineProps } from "vue";
import TextArea from "@/Components/TextArea.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps<{
    event: {
        id: number;
        summary: string;
    };
}>();

const emit = defineEmits(["feedback-sent"]);

const feedbackText = ref("");
const hasReacted = ref(false);
const reaction = ref<"like" | "dislike" | null>(null);
const feedbackSent = ref(false);

const handleReaction = (type: "like" | "dislike") => {
    reaction.value = type;
    hasReacted.value = true;
};

const sendFeedback = () => {
    window.axios
        .post(route("feedback"), {
            ics_event_id: props.event.id,
            like: reaction.value === "like",
            data: feedbackText.value,
        })
        .then((res) => {
            feedbackSent.value = true;
        })
        .catch((error) => {
            console.error(error);
            feedbackSent.value = true;
        });

    console.log("Feedback:", feedbackText.value);
    console.log("Reaction:", reaction.value);
};

const clearAndCloseForm = () => {
    feedbackText.value = "";
    hasReacted.value = false;
    reaction.value = null;
    emit("feedback-sent");
};
</script>

<template>
    <div v-if="!feedbackSent" class="relative flex flex-col gap-5 rounded-md">
        <h2 class="text-center text-xl font-medium text-gray-900 dark:text-gray-100">{{ event.summary }}</h2>

        <TextArea
            v-model="feedbackText"
            :disabled="hasReacted"
            :placeholder="$t('feedback.placeholder')"
            class="mb-4 w-full resize-none rounded-md border p-2 disabled:opacity-70"
            rows="4"
        ></TextArea>

        <div v-if="!hasReacted && feedbackText" class="flex justify-center gap-8">
            <div
                class="inline-flex cursor-pointer items-center gap-4 rounded-md bg-green-700 px-4 py-3 text-sm font-semibold tracking-widest text-white uppercase
                    shadow-sm transition duration-150 ease-in-out hover:bg-green-600 hover:text-gray-50 disabled:opacity-25 dark:text-gray-200"
                @click="handleReaction('like')"
            >
                <img class="size-6" src="/like.png" alt="like" />
            </div>

            <div
                class="inline-flex cursor-pointer items-center gap-4 rounded-md bg-red-600/60 px-4 py-3 text-sm font-semibold tracking-widest text-gray-300
                    uppercase shadow-sm transition duration-150 ease-in-out"
                @click="handleReaction('dislike')"
            >
                <img class="size-6" src="/dislike.png" alt="dislike" />
            </div>
        </div>

        <!-- Send Button -->
        <div v-if="hasReacted" class="flex justify-center">
            <PrimaryButton
                class="inline-flex cursor-pointer items-center gap-4 rounded-md px-4 py-3 !text-sm font-semibold tracking-widest text-gray-300 uppercase shadow-sm
                    transition duration-150 ease-in-out"
                @click="sendFeedback"
            >
                <img
                    class="size-6"
                    :src="reaction === 'like' ? '/like.png' : '/dislike.png'"
                    :alt="reaction === 'like' ? 'like' : 'dislike'"
                />
                <span>{{ $t("feedback.send") }}</span>
            </PrimaryButton>
        </div>
    </div>
    <div v-else class="relative flex cursor-pointer flex-col gap-5 rounded-md" @click="clearAndCloseForm">
        <h2 class="mb-4 text-center text-xl font-medium tracking-widest text-gray-900 uppercase dark:text-gray-100">
            {{ reaction === "like" ? $t("feedback.like.title") : $t("feedback.dislike.title") }}
        </h2>
        <img
            :alt="reaction === 'like' ? 'happy' : 'sad'"
            class="m-auto mb-6 h-36"
            :src="reaction === 'like' ? '/calendar.png' : '/calendar-sad.png'"
        />

        <p class="text-center text-lg text-gray-600 dark:text-gray-300">
            {{ reaction === "like" ? $t("feedback.like.description") : $t("feedback.dislike.description") }}
        </p>
    </div>
</template>
