<template>
    <div class="container mx-auto px-4 py-6 text-center">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Attendees for {{ event.name }}</h1>

        <!-- Date Filter -->
        <div class="mb-4 flex flex-col sm:flex-row justify-center items-center space-y-3 sm:space-y-0">
            <select id="date-filter" v-model="selectedDate"
                class="border py-2 px-8 sm:px-9 rounded-lg text-sm sm:text-base">
                <option v-for="(date, index) in dateRange" :key="index" :value="date">
                    {{ formatDate(date) }}
                </option>
            </select>
            <span class="mt-2 sm:mt-0 sm:ml-4 text-gray-600 text-sm sm:text-base">
                Total Attendees: {{ filteredAttendeeRecords.length }}
            </span>
        </div>

        <div v-if="filteredAttendeeRecords.length">
            <div class="overflow-x-auto"> <!-- Enable horizontal scroll on small screens -->
                <table class="min-w-full table-auto bg-white shadow-md rounded-lg overflow-hidden">
                    <thead class="bg-gray-200 text-gray-600 uppercase text-xs sm:text-sm leading-normal">
                        <tr>
                            <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">#</th>
                            <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">Full Name</th>

                            <!-- Conditionally display either 'Attendance' or 'Check-In', 'Check-Out' based on event type -->
                            <template v-if="isSpecialEventType(event.type)">
                                <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">Attendance</th>
                            </template>
                            <template v-else>
                                <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">Check-In</th>
                                <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">Check-Out</th>
                            </template>

                            <th class="py-2 sm:py-3 px-2 sm:px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-xs sm:text-sm text-center">
                        <tr v-for="(attendee_record, index) in filteredAttendeeRecords"
                            :key="attendee_record.attendee_record_id"
                            class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-2 sm:py-3 px-2 sm:px-6">{{ index + 1 }}</td> <!-- Numbering -->

                            <td class="py-2 sm:py-3 px-2 sm:px-6">{{ attendee_record.master_list_member.full_name }}
                            </td>

                            <!-- Conditional for event type -->
                            <td class="py-2 sm:py-3 px-2 sm:px-6">
                                <template v-if="isSpecialEventType(event.type)">
                                    {{ attendee_record.single_signin ? new
                                        Date(attendee_record.single_signin).toLocaleTimeString() : 'Not Signed-in' }}
                                </template>
                                <template v-else>
                                    {{ attendee_record.check_in ? new
                                        Date(attendee_record.check_in).toLocaleTimeString() : 'Not Checked-in' }}
                                </template>
                            </td>

                            <!-- Only display the 'Check-Out' column if it's not a special event type -->
                            <template v-if="!isSpecialEventType(event.type)">
                                <td class="py-2 sm:py-3 px-2 sm:px-6">
                                    {{ attendee_record.check_out ? new
                                        Date(attendee_record.check_out).toLocaleTimeString() : 'Not Checked-out' }}
                                </td>
                            </template>

                            <td class="py-2 sm:py-3 px-2 sm:px-6">
                                <Link
                                    :href="`/events/${event.event_id}/attendees/${attendee_record.attendee_record_id}`"
                                    method="delete" as="button"
                                    class="text-red-500 hover:text-red-700 text-xs sm:text-base">
                                Delete
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-else class="mt-6">
            <p class="text-gray-500 text-xs sm:text-sm">No attendee records found for this date.</p>
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

// Generate a date range from the start date to the end date, adjusting to Manila time zone
const dateRange = ref([]);

const generateDateRange = (startDate, endDate) => {
    const dates = [];
    const start = new Date(startDate);
    const end = new Date(endDate);

    // Adjust to Manila time zone (UTC+8)
    start.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    // Generate the date range including the end date
    while (start <= end) {
        // Adjust the start date to UTC+8 (Manila time)
        const adjustedDate = new Date(start.getTime() + (8 * 60 * 60 * 1000)); // Add 8 hours for UTC+8
        dates.push(adjustedDate.toISOString().split('T')[0]);
        start.setDate(start.getDate() + 1); // Increment date by 1 day
    }

    return dates;
};

// Initialize the date range with event start and end date
dateRange.value = generateDateRange(props.event.start_date, props.event.end_date);

// Filter attendee records by the selected date, adjust the `created_at` to Manila timezone
const filteredAttendeeRecords = computed(() => {
    return props.attendee_records.filter(record => {
        if (!record.created_at) return false;

        // Adjust `created_at` to Manila time zone (UTC+8)
        const recordDate = new Date(new Date(record.created_at).getTime() + (8 * 60 * 60 * 1000))
            .toISOString().split('T')[0];

        // Compare with selectedDate
        return recordDate === selectedDate.value;
    });
});

// Helper function to format dates
const formatDate = (date) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString(undefined, options);
};

// Function to check if the event type is one of the special types
const isSpecialEventType = (eventType) => {
    return /lecture|class orientation|quiz|laboratory|return output|exam/i.test(eventType);
};
</script>
