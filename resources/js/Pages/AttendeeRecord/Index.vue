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
                            <th class="py-3 px-6 text-center">Full Name</th>

                            <!-- Conditionally display either 'Attendance' or 'Check-In', 'Check-Out' based on event type -->
                            <template v-if="isSpecialEventType(event.type)">
                                <th class="py-3 px-6 text-center">Attendance</th>
                            </template>
                            <template v-else>
                                <th class="py-3 px-6 text-center">Check-In</th>
                                <th class="py-3 px-6 text-center">Check-Out</th>
                            </template>

                            <th class="py-3 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm text-start">
                        <tr v-for="attendee_record in filteredAttendeeRecords" :key="attendee_record.attendee_record_id"
                            class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-center">{{ attendee_record.master_list_member.full_name }}</td>

                            <!-- Conditional for event type -->
                            <td class="py-3 px-6 text-center">
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
                                <td class="py-3 px-6 text-center">
                                    {{ attendee_record.check_out ? new
                                        Date(attendee_record.check_out).toLocaleTimeString() : 'Not Checked-out' }}
                                </td>
                            </template>

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

// Generate a date range from the start date to the end date
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
        dates.push(new Date(start.getTime() - start.getTimezoneOffset() * 60000).toISOString().split('T')[0]);
        start.setDate(start.getDate() + 1); // Increment date by 1 day
    }

    return dates;
};

// Initialize the date range with event start and end date
dateRange.value = generateDateRange(props.event.start_date, props.event.end_date);


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

// Function to check if the event type is one of the special types
const isSpecialEventType = (eventType) => {
    return /exam|class orientation|class attendance/i.test(eventType);
};
</script>
