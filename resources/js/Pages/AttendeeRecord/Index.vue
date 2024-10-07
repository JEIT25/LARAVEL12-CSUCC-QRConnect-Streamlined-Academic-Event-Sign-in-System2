<template>
    <div class="container mx-auto px-4 py-6 text-center">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Attendees for {{ event.name }}</h1>

        <!-- Date Filter -->
        <div class="mb-4 flex justify-center items-center">
            <select id="date-filter" v-model="selectedDate" class="border py-2 px-9 rounded-lg">
                <option v-for="(date, index) in dateRange" :key="index" :value="date">
                    {{ formatDate(date) }}
                </option>
            </select>
        </div>

        <div v-if="filteredAttendeeRecords.length">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <tr>
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Check-in</th>
                            <th class="py-3 px-6 text-left">Check-out</th>
                            <th class="py-3 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm text-start">
                        <tr v-for="attendee_record in filteredAttendeeRecords" :key="attendee_record.attendee_record_id"
                            class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6">{{ attendee_record.master_list_member.full_name }}</td>
                            <td class="py-3 px-6">
                                {{ attendee_record.check_in ? new Date(attendee_record.check_in).toLocaleTimeString() :
                                'Not Checked-in' }}
                            </td>
                            <td class="py-3 px-6">
                                {{ attendee_record.check_out ? new Date(attendee_record.check_out).toLocaleTimeString()
                                : 'Not Checked-out' }}
                            </td>
                            <td class="py-3 px-6 text-center">
                                <Link
                                    :href="`/events/${event.event_id}/attendees/${attendee_record.attendee_record_id}`"
                                    method="delete" as="button" class="text-red-500 hover:text-red-700">
                                Delete
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="mt-6">
            <p class="text-gray-500">No attendee records found for this date.</p>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

// Props received
const props = defineProps({
    event: Object,
    attendee_records: Array,
});

// State for selected date, default to event start date
const selectedDate = ref(props.event.start_date);

// Generate a date range from the start date to the current date
const dateRange = ref([]);

// Function to generate date range between event start date and today
const generateDateRange = (startDate) => {
    const dates = [];
    const start = new Date(startDate);
    const today = new Date();

    while (start <= today) {
        dates.push(start.toISOString().split('T')[0]);
        start.setDate(start.getDate() + 1); // Increment date by 1 day
    }

    return dates;
};

// Initialize the date range
dateRange.value = generateDateRange(props.event.start_date);

// Filter attendee records by the selected date
const filteredAttendeeRecords = computed(() => {
    return props.attendee_records.filter(record => {
        const createdAtDate = record.created_at ? record.created_at.split('T')[0] : null;
        return createdAtDate === selectedDate.value;
    });
});

// Helper function to format dates
const formatDate = (date) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString(undefined, options);
};
</script>



