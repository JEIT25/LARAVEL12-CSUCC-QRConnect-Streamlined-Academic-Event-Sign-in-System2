<template>
    <div class="min-h-screen"> <!-- Gray background extended to the entire screen -->
        <div class="container mx-auto px-4 py-6">

            <div v-if="showReturnOutputsModal"
                class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 overflow-y-auto">
                <div
                    class="bg-white p-6 rounded shadow-lg max-w-6xl w-full text-center overflow-auto max-h-screen relative">

                    <!-- Close Button -->
                    <button @click="showReturnOutputsModal = false"
                        class="absolute top-4 right-4 bg-red-500 text-white h-8 w-20 flex items-center justify-center">
                        Close
                    </button>

                    <!-- Search and Added Events Sections -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full min-h-screen">

                        <!-- Left Column: Search Results -->
                        <div>
                            <div class="border-b border-gray-300 pb-4 mb-6">
                                <h1 class="text-xl font-semibold mb-4">Search for Events</h1>
                                <input id="search" v-model="searchQuery" class="w-full p-2 border rounded"
                                    placeholder="Search for return output events..." />
                            </div>

                            <div class="border-b border-gray-300 pb-4 mb-6">
                                <h1 class="text-xl font-semibold mb-4">Select Date Range</h1>
                                <div class="grid grid-cols-2 gap-4">
                                    <!-- Start Date -->
                                    <div>
                                        <label for="searchStartDate"
                                            class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" id="searchStartDate" v-model="searchStartDate"
                                            class="w-full p-2 border rounded text-sm" />
                                    </div>
                                    <!-- End Date -->
                                    <div>
                                        <label for="searchEndDate"
                                            class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" id="searchEndDate" v-model="searchEndDate"
                                            class="w-full p-2 border rounded text-sm" />
                                    </div>
                                </div>
                            </div>

                            <h2 class="text-lg font-semibold mt-6">Search Results</h2>
                            <ul class="space-y-4 max-h-screen overflow-y-auto border-b border-gray-300 pb-4">
                                <li v-for="event in filteredEvents" :key="event.event_id"
                                    class="p-4 border rounded bg-gray-100 flex flex-wrap md:flex-nowrap items-center justify-between gap-4">
                                    <!-- Event Name -->
                                    <span class="block text-sm md:text-base font-medium text-gray-700">{{ event.name
                                        }}</span>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col flex-wrap gap-2 md:gap-4">
                                        <button @click="setCurrentEvt(event)"
                                            class="px-4 py-2 bg-blue-500 text-white rounded text-sm md:text-base hover:bg-blue-600">
                                            Details
                                        </button>
                                        <button @click="addToReport(event)"
                                            class="px-4 py-2 bg-green-500 text-white rounded text-sm md:text-base hover:bg-green-600">
                                            Add
                                        </button>
                                    </div>
                                </li>
                            </ul>

                            <!-- Submit Button -->
                            <button @click="submitReturnOutputForm"
                                class="mt-4 w-full px-4 py-2 bg-green-600 text-white text-lg font-semibold rounded hover:bg-green-700">
                                Submit
                            </button>
                        </div>

                        <!-- Right Column: Added Events -->
                        <div>
                            <h1 class="text-xl font-semibold mt-8 mb-4">Added Events</h1>
                            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                                <!-- Quizzes Section -->
                                <div class="border-t border-gray-300 pt-4">
                                    <h2 class="text-lg font-semibold mt-6 mb-4">Quizzes ({{ numOfQuizEvts }})</h2>
                                    <ul class="space-y-4 max-h-48 overflow-y-auto border-b border-gray-300 pb-4">
                                        <li v-for="event in quizzes" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1">{{ event.name }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition-all">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition-all">
                                                    Delete
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Laboratories Section -->
                                <div class="border-t border-gray-300 pt-4">
                                    <h2 class="text-lg font-semibold mt-6 mb-4">Laboratories ({{ numOfLabEvts }})</h2>
                                    <ul class="space-y-4 max-h-48 overflow-y-auto border-b border-gray-300 pb-4">
                                        <li v-for="event in laboratories" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1">{{ event.name }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition-all">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition-all">
                                                    Delete
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Exams Section -->
                                <div class="border-t border-gray-300 pt-4">
                                    <h2 class="text-lg font-semibold mt-6 mb-4">Exams ({{ numOfExamEvts }})</h2>
                                    <ul class="space-y-4 max-h-48 overflow-y-auto border-b border-gray-300 pb-4">
                                        <li v-for="event in exams" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1">{{ event.name }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded transition-all">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded transition-all">
                                                    Delete
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limit Reached Pop-up Message -->
                    <div v-if="limitReached"
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                        <div class="bg-white p-6 rounded shadow-lg max-w-md w-full text-center">
                            <h2 class="text-xl font-bold mb-4">Limit Reached</h2>
                            <p class="mb-4">{{ maxReturnOutputMes }}.</p>
                            <button @click="limitReached = false" class="px-4 py-2 bg-red-500 text-white rounded">
                                Close
                            </button>
                        </div>
                    </div>

                    <!-- Event Details Modal -->
                    <div v-if="showEvtDetailsModal"
                        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-70 z-50 px-4">
                        <div class="bg-white p-8 rounded-lg shadow-2xl w-full max-w-lg sm:max-w-md">
                            <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Event Details</h2>
                            <div class="space-y-5 text-gray-800">
                                <div class="flex flex-col capitalize">
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Name:</strong> {{
                                        currentEvtToShow.name.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Type:</strong> {{
                                        currentEvtToShow.type }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Description:</strong>
                                        {{ currentEvtToShow.description.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Location:</strong> {{
                                        currentEvtToShow.location.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Start Date:</strong>
                                        {{ currentEvtToShow.start_date }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">End Date:</strong>
                                        {{ currentEvtToShow.end_date }}</p>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-center">
                                <button @click="showEvtDetailsModal = false"
                                    class="px-6 py-3 bg-red-500 hover:bg-red-600 text-white text-lg font-medium rounded-lg shadow-md transition ease-in-out duration-300">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>




            <!-- Title -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <h1 class="text-2xl sm:text-3xl font-bold text-left">Active Assessment</h1>

                <div class="w-1/3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <Link as="button" method="get" href="/events/create"
                        class="w-full sm:w-48 bg-amber-500 hover:bg-blue-950 hover:text-white text-gray font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4 sm:mt-0 duration-300 ease-in-out">
                    Create an event
                    </Link>
                    <button as="button" v-on:click="setReturnOuputModal"
                        class="w-full sm:w-48 bg-amber-500 hover:bg-blue-950 hover:text-white text-gray font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4 sm:mt-0 duration-300 ease-in-out">
                        Returned Ouputs
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="event in events" :key="event.event_id" :href="`/events/${event.event_id}`"
                    class="bg-gray-900 text-white shadow-lg rounded-lg p-4 hover:bg-gray-600 transition-colors flex flex-col justify-center">
                <h3 class="text-lg sm:text-xl font-bold">{{ event.name }}</h3>
                <p class="text-gray-200 mb-3 sm:mb-5">Activity Name</p> <!-- Added label -->
                <div class="bg-gray-700 text-white rounded p-3 mb-4">
                    <!-- Darker blue background for the description -->
                    <p>{{ truncateText(event.description, 100) }}</p>
                </div>
                <p v-if="event.program || event.year_level" class="text-sm text-gray-300">
                    <strong>Program & Year Level:</strong> {{ event.program }} {{ event.year_level }}
                </p>
                <p v-if="event.subject || event.subject_code" class="text-sm text-gray-300">
                    <strong>Subject & Code:</strong> {{ event.subject }} {{ event.subject_code }}
                </p>
                <p class="text-sm text-gray-300">
                    <strong>Location:</strong> {{ event.location }}
                </p>
                <p class="text-sm text-gray-300">
                    <strong>Date:</strong> {{ new Date(event.start_date).toLocaleDateString() }}
                </p>
                <p class="text-sm text-gray-300">
                    <strong>End Date:</strong> {{ new Date(event.end_date).toLocaleDateString() }}
                </p>
                </Link>
            </div>
        </div>
    </div>
</template>


<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue'

const props = defineProps({
    events: Array
});


// Method to truncate text
function truncateText(text, maxLength) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}

let numOfQuizEvts = ref(0);
let numOfLabEvts = ref(0);
let numOfExamEvts = ref(0);
let evtsAdded = ref([]);
let showReturnOutputsModal = ref(false);
let currentEvtToShow = ref(null);
let showEvtDetailsModal = ref(false);
let limitReached = ref(false)
let maxReturnOutputMes = ref('')
// Search Query
const searchQuery = ref("");
const searchEndDate = ref("")
const searchStartDate = ref("")


// Watch `evtsAdded` and update the form dynamically
watch(
    evtsAdded,
    (newVal) => {
        returnOutputForm.events = newVal;
    },
    { deep: true } // Ensure deep watch for array changes
);

// Filtered lists for each type (singular and plural)
const quizzes = computed(() =>
    returnOutputForm.quiz = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('quiz') || event.name.toLowerCase().includes('quizzes')
    )
);

const laboratories = computed(() =>
    returnOutputForm.lab = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('laboratory') || event.name.toLowerCase().includes('laboratories')
    )
);

const exams = computed(() =>
    returnOutputForm.exam = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('exam') || event.name.toLowerCase().includes('exams')
    )
);


// Initialize form with the computed property
const returnOutputForm = useForm({
    events: [],
    quiz: [],
    lab: [],
    exam: [],
    start_date: computed(() => {
        return searchStartDate.value
    }),
    end_date: computed(() => {
        return searchEndDate.value
    }),
});

// Filtered Events Computation
const filteredEvents = computed(() => {
    return props.events
        .filter((event) =>
            event.type === 'return output'
        )
        .filter((event) => {
            return event.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        })
        .filter((event) => {
            if (searchStartDate.value != '') {
                // Convert event.start_date to YYYY-MM-DD format
                const formattedStartDate = new Date(event.start_date).toISOString().split('T')[0];
                return formattedStartDate === searchStartDate.value;
            }
            return true
        })
        .filter((event) => {
            if (searchEndDate.value != '') {
                // Convert event.start_date to YYYY-MM-DD format
                const formattedStartDate = new Date(event.end_date).toISOString().split('T')[0];
                return formattedStartDate === searchEndDate.value;
            }
            return true
        })
});


// Event handlers
const setReturnOuputModal = () => {
    showReturnOutputsModal.value = !showReturnOutputsModal.value
}

const addToReport = (event) => {
    if (event.name.toLowerCase().includes('quiz') || event.name.toLowerCase().includes('quizzes')) {
        if (numOfQuizEvts.value >= 5) {
            limitReached.value = true;
            maxReturnOutputMes.value = 'You cannot add any more quiz events'
            return;
        }
        numOfQuizEvts.value++;
    }

    if (event.name.toLowerCase().includes('laboratory') || event.name.toLowerCase().includes('laboratories')) {
        if (numOfLabEvts.value >= 5) {
            limitReached.value = true;
            maxReturnOutputMes.value = 'You cannot add any more laboratory events'
            return;
        }
        numOfLabEvts.value++;
    }

    if (event.name.toLowerCase().includes('exam') || event.name.toLowerCase().includes('exams')) {
        if (numOfExamEvts.value >= 2) {
            limitReached.value = true;
            maxReturnOutputMes.value = 'You cannot add any more exam events'
            return;
        }
        numOfExamEvts.value++;
    }

    // Add the event to the report
    evtsAdded.value.push(event);
};


const setCurrentEvt = (event) => {
    currentEvtToShow.value = event;
    showEvtDetailsModal.value = true;
};

const deleteOneEvtReport = (event) => {
    const index = evtsAdded.value.findIndex((evt) => evt.event_id === event.event_id);
    if (index !== -1) evtsAdded.value.splice(index, 1);

    const name = event.name.toLowerCase();

    // Adjust counters based on the event name
    switch (true) {
        case name.includes('quiz') || name.includes('quizzes'):
            if (numOfQuizEvts.value > 0) {
                numOfQuizEvts.value--;
            }
            break;

        case name.includes('laboratory') || name.includes('laboratories'):
            if (numOfLabEvts.value > 0) {
                numOfLabEvts.value--;
            }
            break;

        case name.includes('exam') || name.includes('exams'):
            if (numOfExamEvts.value > 0) {
                numOfExamEvts.value--;
            }
            break;

        default:
            console.log("Event type does not match quiz, laboratory, or exam.");
            break;
    }
};


// Submit Form Handler using form.post()
const submitReturnOutputForm = () => {
    returnOutputForm.post('/export-attendee-records/return-outputs');
};

</script>

<style scoped>
/* Add 3D effect */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
