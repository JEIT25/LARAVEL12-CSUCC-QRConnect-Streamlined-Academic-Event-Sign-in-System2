<template>
    <div class="flex justify-center items-center min-h-screen"> <!-- Ensure full height with gray background -->
        <!-- set default photo of the event -->
        <div class="p-10 w-full max-w-6xl"> <!-- Wrapper for padding and width -->
            <!-- Event Image -->
            <div class="flex justify-center mb-6">
                <img :src="event.profile_image || props.default_image" alt="Event Profile Image"
                    class="w-full h-60 rounded-lg shadow-md"
                    :class="event.profile_image ? 'object-cover' : 'object-contain'"> <!-- Change w-md to w-full -->
            </div>

            <!-- Event Details -->
            <div class="mb-6 text-center">
                <h2 class="text-3xl font-bold text-gray-800">{{ event.name }}</h2>
                <p class="text-sm text-gray-500 mb-4 text-left mt-5">{{ event.description }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Start Date</label>
                    <p class="text-gray-700">{{ new Date(event.start_date).toLocaleDateString() }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">End Date</label>
                    <p class="text-gray-700">{{ new Date(event.end_date).toLocaleDateString() }}</p>
                </div>
                <div v-if="event.subject">
                    <label class="block text-sm font-medium text-gray-500">Subject</label>
                    <p class="text-gray-700">{{ event.subject }}</p>
                </div>
                <div v-if="event.subject_code">
                    <label class="block text-sm font-medium text-gray-500">Subject Code</label>
                    <p class="text-gray-700">{{ event.subject_code }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Type</label>
                    <p class="text-gray-700">{{ event.type }}</p>
                </div>
                <div v-if="event.school_year">
                    <label class="block text-sm font-medium text-gray-500">School Year</label>
                    <p class="text-gray-700">{{ event.school_year }}</p>
                </div>
                <div v-if="event.semester">
                    <label class="block text-sm font-medium text-gray-500">Semester</label>
                    <p class="text-gray-700">{{ event.semester }}</p>
                </div>
                <!-- Adding Program and Year Level -->
                <div v-if="event.program">
                    <label class="block text-sm font-medium text-gray-500">Program</label>
                    <p class="text-gray-700">{{ event.program }}</p>
                </div>
                <div v-if="event.year_level">
                    <label class="block text-sm font-medium text-gray-500">Year Level</label>
                    <p class="text-gray-700">{{ event.year_level }}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div v-if="user.type === 'facilitator'" class="relative mb-6 space-y-6"> <!-- Space between sections -->

                <!-- Upper right for Edit and Delete buttons (in a flex row layout) -->
                <div class="flex justify-end space-x-4">
                    <!-- Flex to align both buttons horizontally, positioned at the top right -->
                    <Link :href="`/events/${event.event_id}/edit`" class="btn-primary" as="button" method="get">
                    Edit
                    </Link>
                    <Link :href="`/events/${event.event_id}`" class="btn-primary" as="button" method="delete">
                    Delete
                    </Link>
                </div>

                <!-- Wrap all the other buttons in a single container -->
                <div class="bg-gray-200 p-6 rounded-lg">
                    <!-- Inner div split in two (left and right sections) with a gap -->
                    <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-8">
                        <!-- Added space-x-8 for horizontal gap -->

                        <!-- Left section for Show Masterlist, View Attendance, Export Attendance -->
                        <div class="flex flex-col space-y-4 w-full md:w-1/2">
                            <!-- Flex column for stacking these buttons -->
                            <h3 class="text-lg font-bold mb-2 text-gray-800 text-center">Attendance Management</h3>
                            <!-- Title for left section -->
                            <Link v-if="master_list"
                                :href="`/events/${event.event_id}/master-lists/${master_list.master_list_id}`"
                                class="btn-primary" as="button" method="get">
                            Show MasterList
                            </Link>
                            <button v-else @click="createMasterList" class="btn-primary">
                                Create MasterList
                            </button>
                            <Link :href="`/events/${event.event_id}/attendees`" class="btn-primary" as="button"
                                method="get">
                            View Attendance List
                            </Link>
                        </div>

                        <!-- Right section for Check-In, Check-Out (attendance actions) -->
                        <div class="flex flex-col space-y-4 w-full md:w-1/2"> <!-- Flex column to stack buttons -->
                            <h3 class="text-lg font-bold mb-2 text-gray-800 text-center">Attendance Actions</h3>
                            <!-- Title for this section -->
                            <Link
                                v-if="!event.type.includes('exam') && !event.type.includes('class attendance') && !event.type.includes('class orientation')"
                                :href="`/events/${event.event_id}/qrscanner/checkin`" class="btn-primary" as="button">
                            Check-In
                            </Link>
                            <Link
                                v-if="!event.type.includes('exam') && !event.type.includes('class attendance') && !event.type.includes('class orientation')"
                                :href="`/events/${event.event_id}/qrscanner/checkout`" class="btn-primary" as="button">
                            Check-Out
                            </Link>
                            <Link
                                v-if="event.type.includes('exam') || event.type.includes('class attendance') || event.type.includes('class orientation')"
                                :href="`/events/${event.event_id}/qrscanner/single-signin`" class="btn-primary"
                                as="button" method="get">
                            Single Sign-in
                            </Link>
                            <button @click="showExportModal = true" class="btn-primary">
                                Export Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Export Modal (Overlay) -->
            <div v-if="showExportModal"
                class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <h3 class="text-lg font-bold mb-4">Select Export Options</h3>
                    <form @submit.prevent="exportAttendance">
                        <!-- Template Selector -->
                        <div class="mb-4">
                            <label for="template" class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                            <select v-model="selectedTemplate" id="template" class="w-full p-2 border rounded-md">
                                <option value="" disabled>Select a template</option>
                                <option v-if="event.type.includes('class orientation')" value="class-orientation">Class
                                    Orientation</option>
                                <option v-if="event.type.includes('class attendance')" value="class-attendance-excel">
                                    Class
                                    Attendance Excel</option>
                                <option v-if="event.type.includes('class attendance')" value="class-attendance-pdf">
                                    Class
                                    Attendance PDF</option>
                                <option v-if="event.type.includes('exam')" value="midterm-exam">Midterm Exam</option>
                                <option v-if="event.type.includes('exam')" value="final-exam">Final Exam</option>
                                <option
                                    v-if="!event.type.includes('class attendance') && !event.type.includes('class orientation') && !event.type.includes('exam')"
                                    value="general-template">Event
                                    Attendance (Check-in & Checkout)</option>
                            </select>
                        </div>

                        <!-- Date Selector -->
                        <div class="mb-4">
                            <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                            <select v-model="selectedDate" id="date" class="w-full p-2 border rounded-md">
                                <option disabled value="">Select Date</option>
                                <option v-if="!(props.event.start_date == props.event.end_date)" value="all">All Dates
                                </option>
                                <option v-for="date in generateDateRange(event.start_date,event.end_date)" :key="date"
                                    :value="date">
                                    {{ formatDate(date) }}
                                </option>
                            </select>
                        </div>

                        <!-- Invigilator Name Field -->
                        <div class="mb-4" v-if="event.type.includes('exam')">
                            <label for="invigilator" class="block text-sm font-medium text-gray-700 mb-2">Invigilator
                                Name</label>
                            <input v-model.trim="invigilatorName" type="text" id="invigilator"
                                class="w-full p-2 border rounded-md" placeholder="Enter Invigilator Name">
                        </div>

                        <!-- Time Selector Fields -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start
                                Time</label>
                            <input v-model.trim="startTime" type="time" id="start_time"
                                class="w-full p-2 border rounded-md" placeholder="Select Start Time">
                        </div>

                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                            <input v-model.trim="endTime" type="time" id="end_time" class="w-full p-2 border rounded-md"
                                placeholder="Select End Time">
                        </div>


                        <!-- Modal Buttons -->
                        <div class="flex justify-end">
                            <button type="button" @click="showExportModal = false" class="btn-secondary mr-2">
                                Cancel
                            </button>
                            <button type="submit" class="btn-primary">
                                Export
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props
const props = defineProps({
    event: Object,
    master_list: Object,
    user: Object,
    default_image: String
});

// Reactive state
const form = useForm({});
const showExportModal = ref(false);
const selectedTemplate = ref(''); // Holds selected template
const selectedDate = ref(''); // Holds selected date for export
const invigilatorName = ref(''); // Holds the invigilator name
const startTime = ref(''); // Holds the start time
const endTime = ref(''); // Holds the end time


// Function to create a master list
const createMasterList = () => {
    form.post(`/events/${props.event.event_id}/master-lists`);
};

// Function to generate a range of dates from the start_date to event_date
const generateDateRange = (startDate,endDate) => {
    const dates = [];
    const start = new Date(startDate);
    const end = new Date(endDate);

    // Set the time of both dates to midnight to only compare the date part
    start.setHours(0, 0, 0, 0);
    end.setHours(0, 0, 0, 0);

    // Generate the date range including the end date
    while (start <= end ) {
        dates.push(new Date(start.getTime() - start.getTimezoneOffset() * 60000).toISOString().split('T')[0]);
        start.setDate(start.getDate() + 1); // Increment date by 1 day
    }

    return dates;
};


// Function to format dates for display
const formatDate = (date) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(date).toLocaleDateString(undefined, options);
};

// Function to handle attendance export
const exportAttendance = () => {
    if (selectedTemplate.value && selectedDate.value) {
        const queryParams = `?date=${selectedDate.value}&invigilator=${invigilatorName.value}&start_time=${startTime.value}&end_time=${endTime.value}`;
        // Redirect to the export route, passing the selected template, date, invigilator, start time, and end time
        window.open(`/export-attendee-records/${props.event.event_id}/${selectedTemplate.value}${queryParams}`, '_blank');
        showExportModal.value = false; // Close the modal after submission
    }
};

</script>
