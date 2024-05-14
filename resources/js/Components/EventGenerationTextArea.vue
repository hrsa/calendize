<script setup lang="ts">
import { onMounted, ref } from 'vue';
import TextArea from "@/Components/TextArea.vue";
import LoadingSpinner from "@/Components/LoadingSpinner.vue";

const model = defineModel<string>({ required: true });

const props = defineProps<{
    loading?: boolean;
}>();

const getPlaceholder = (): string => {
const placeholderTexts: string[] = [
        "Note to self:\nBuy anniversary gift for Sarah\nMay 20, 2024\nDon't forget to buy flowers too!!",
        "Hotel Reservation Confirmation:\nGuest:\n- Name: Emily Johnson\n- Email: emily.johnson@example.com\n- Phone: +1 (555) 987-6543\n\nReservation Details:\n- Check-in Date: June 20, 2024\n- Check-out Date: June 25, 2024\n- Number of Nights: 5\n- Room Type: Deluxe King Room\n- Number of Guests: 2\n- Special Requests: Non-smoking room, high floor\n\nHotel Information:\n- Hotel Name: Serenity Resort\n- Address: 123 Beachfront Avenue, Anytown, USA\n- Phone: +1 (555) 123-4567\n- Website: www.serenityresort.com",
        "Appointment Confirmation:\n- Name: John Smith\n- Email: john.smith@example.com\n- Phone: +1 (555) 123-4567\n\nAppointment Details:\n- Date: June 10, 2024\n- Time: 2:00 PM\n- Location: City Hall\n- Purpose: Property Tax Rebate",
        "Concert Ticket Holder:\n- Name: Jane Smith\n- Email: jane.smith@example.com\n- Phone: +1 (555) 987-6543\n\nEvent Details:\n- Event: Summer Music Festival\n- Date: July 15, 2024\n- Time: 7:00 PM\n- Venue: Harmony Park Amphitheater\n- Location: Anytown, USA\n\nTicket Information:\n- Ticket Type: General Admission\n- Ticket Number: 123456789\n- Seat Number: N/A",
        "Flight Details:\n- Departure Date: May 25, 2024\n- Departure Time: 10:00 AM\n- Departure Airport: JFK - John F. Kennedy International Airport\n- Arrival Date: May 30, 2024\n- Arrival Time: 3:00 PM\n- Arrival Airport: LAX - Los Angeles International Airport\n- Flight Number: XYZ123\n- Seat Number: 22A\n",
        "Reminder to Self:\n Action: Schedule dentist appointment\nDate: May 15, 2024\nPriority: High\nLocation: Downtown Dental Clinic\nNote: Call to schedule appointment for annual check-up.",
        "Hey there! How about dinner on Saturday? Let's meet at The Rustic Kitchen at 7:00 PM.\nAddress: 456 Oak Street, Anytown, USA.\nCan't wait to catch up!\n\nXOXO\nFred"
    ];
    const index: number = Math.floor(Math.random() * placeholderTexts.length);
    return placeholderTexts[index];
}
</script>

<template>
    <div class="relative h-48 w-full">
                                <TextArea class="h-48 w-full resize-none placeholder:text-sm text-lg" :class="props.loading ? 'blur-lg' : ''"
                                          :placeholder="getPlaceholder()"
                                          v-model="model"/>
                                <div v-if="props.loading"
                                     role="status" class="absolute top-2/4 left-1/2 -translate-x-1/2 -translate-y-1/2">
                                    <LoadingSpinner/>
                                </div>
                            </div>
</template>
