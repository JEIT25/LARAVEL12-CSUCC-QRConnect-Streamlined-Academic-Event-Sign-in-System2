<template>
    <div class="min-h-screen"> <!-- Gray background extended to the entire screen -->
        <div class="container mx-auto px-4 py-6">

            <div v-if="showReturnOutputsModal" @click.self="setReturnOuputModal"
                class=" fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 overflow-y-auto">
                <div
                    class="bg-white p-6 rounded shadow-lg max-w-6xl w-full text-center overflow-auto max-h-screen relative">

                    <!-- Close Button -->
                    <button @click="setReturnOuputModal"
                        class="absolute top-4 right-4 btn-error h-8 w-20 flex items-center justify-center">
                        Close
                    </button>

                    <!-- Search and Added Events Sections -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full min-h-screen">

                        <!-- Left Column: Search Results -->
                        <div>
                            <!-- Search Bar -->
                            <div class="border-b border-gray-300 pb-4 mb-6">
                                <h1 class="text-xl font-semibold mb-4">Search for Events</h1>
                                <input id="search" v-model="searchQuery" class="w-full p-2 border rounded"
                                    placeholder="Search for return output events..." />
                            </div>

                            <!-- Event Filters -->
                            <h1 class="text-xl font-semibold mb-4">Event Filters</h1>
                            <div
                                class="border-b border-gray-300 pb-4 mb-6 space-y-4 sm:space-y-0 sm:grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                <!-- School Year Selector -->
                                <div>
                                    <label for="schoolYear" class="block text-sm font-medium text-gray-700">Select
                                        School Year</label>
                                    <select v-model="selectedSchoolYear" id="schoolYear"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value=""></option>
                                        <option v-for="(schoolYear, index) in uniqueSchoolYear" :key="index"
                                            :value="schoolYear">
                                            {{ schoolYear }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Semester Selector -->
                                <div>
                                    <label for="semester" class="block text-sm font-medium text-gray-700">Select
                                        Semester</label>
                                    <select id="semester" v-model="selectedSemester"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value=""></option>
                                        <option value="1st">1</option>
                                        <option value="2nd">2</option>
                                    </select>
                                </div>

                                <!-- Subject Selector -->
                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700">Select
                                        Subject</label>
                                    <select id="subject" v-model="selectedSubject"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                        <option value=""></option>
                                        <option v-for="subject in uniqueSubjects" :key="subject" :value="subject">
                                            {{ subject }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date Filters -->
                                <div>
                                    <label for="searchStartDate"
                                        class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date" id="searchStartDate" v-model="searchStartDate"
                                        class="w-full p-2 border rounded text-sm" />
                                </div>
                                <div>
                                    <label for="searchEndDate" class="block text-sm font-medium text-gray-700 mb-1">End
                                        Date</label>
                                    <input type="date" id="searchEndDate" v-model="searchEndDate"
                                        class="w-full p-2 border rounded text-sm" />
                                </div>

                                <!-- Event Type Checkboxes -->
                                <div class="col-span-full">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Event Type</p>
                                    <div class="flex flex-wrap gap-4 items-center justify-center">
                                        <div>
                                            <input type="checkbox" id="lab" class="mr-2" v-model="labCheck" />
                                            <label for="lab" class="text-sm">Lab</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="quiz" class="mr-2" v-model="quizCheck" />
                                            <label for="quiz" class="text-sm">Quiz</label>
                                        </div>
                                        <div>
                                            <input type="checkbox" id="exam" class="mr-2" v-model="examCheck" />
                                            <label for="exam" class="text-sm">Exam</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Attendance Date -->
                            <div class="border-b border-gray-300 pb-4 mb-6">
                                <h1 class="text-xl font-semibold mb-4">Select Attendance Date</h1>
                                <div>
                                    <label for="attendanceDate"
                                        class="block text-sm font-medium text-gray-700 mb-1 w-">This will be used as
                                        basis for determining attendance of students</label>
                                    <input type="date" id="attendanceDate" v-model="attendanceDate"
                                        class="w-96 p-2 border rounded text-sm text-center" />
                                </div>
                            </div>

                            <!-- Search Results -->
                            <h2 class="text-lg font-semibold mt-6">Search Results</h2>
                            <ul class="space-y-4 max-h-screen overflow-y-auto border-b border-gray-300 pb-4">
                                <!-- Message for no selected school year, semester, subject -->
                                <div v-if="!selectedSchoolYear" class="text-red-500 text-center">
                                    Please select a school year first.
                                </div>
                                <div v-if="!selectedSemester" class="text-red-500 text-center">
                                    Please select a semester first.
                                </div>
                                <div v-if="!selectedSubject" class="text-red-500 text-center">
                                    Please select a subject first.
                                </div>

                                <!-- Event List -->
                                <li v-else v-for="event in filteredEvents" :key="event.event_id"
                                    class="p-4 border rounded bg-gray-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <!-- Event Name -->
                                    <span class="block text-sm sm:text-base font-medium text-gray-700 w-full sm:w-auto">
                                        {{ event.name }}
                                    </span>
                                    <!-- Action Buttons -->
                                    <div class="flex flex-col sm:flex-row gap-2 sm:gap-4 w-full sm:w-auto">
                                        <button @click="setCurrentEvt(event)"
                                            class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base btn-primary">
                                            Details
                                        </button>
                                        <button @click="addToReport(event)"
                                            class="w-full sm:w-auto px-4 py-2 text-sm sm:text-base btn-success">
                                            Add
                                        </button>
                                    </div>
                                </li>
                            </ul>

                            <!-- Submit Button -->
                            <button @click="submitReturnOutputForm"
                                class="mt-4 w-full btn-primary">
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
                                        <li v-for="(event, index) in quizzes" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1"> <span
                                                    class="text-black font-bold">{{ 1 + index }}.</span> {{
                                                event.name
                                                }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="btn-primary">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="btn-error">
                                                    Delete
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Laboratories Section -->
                                <div class="border-t border-gray-300 pt-4">
                                    <h2 class="text-lg font-semibold mt-6 mb-4">Laboratories ({{ numOfLabEvts }})
                                    </h2>
                                    <ul class="space-y-4 max-h-48 overflow-y-auto border-b border-gray-300 pb-4">
                                        <li v-for="(event, index) in laboratories" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1"><span
                                                    class="text-black font-bold">{{ 1 + index }}.</span> {{
                                                event.name
                                                }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="btn-primary transition-all">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="btn-error transition-all">
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
                                        <li v-for="(event, index) in exams" :key="event.event_id"
                                            class="p-4 border rounded bg-gray-100 flex flex-row justify-between items-center space-x-4">
                                            <span class="font-medium text-gray-700 flex-1"><span
                                                    class="text-black font-bold">{{ 1 + index }}.</span> {{
                                                event.name
                                                }}</span>
                                            <div class="flex space-x-2">
                                                <button @click="setCurrentEvt(event)"
                                                    class="btn-primary">
                                                    Details
                                                </button>
                                                <button @click="deleteOneEvtReport(event)"
                                                    class="btn-error">
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
                            <button @click="limitReached = false" class="btn-error">
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
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Semester:</strong> {{
                                        currentEvtToShow.semester.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Type:</strong> {{
                                        currentEvtToShow.type }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Description:</strong>
                                        {{ currentEvtToShow.description.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Location:</strong> {{
                                        currentEvtToShow.location.toLowerCase() }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">Start
                                            Date:</strong>
                                        {{ currentEvtToShow.start_date }}</p>
                                    <p class="text-lg"><strong class="font-semibold text-gray-900">End
                                            Date:</strong>
                                        {{ currentEvtToShow.end_date }}</p>
                                </div>
                            </div>
                            <div class="mt-8 flex justify-center">
                                <button @click="showEvtDetailsModal = false"
                                    class="px-6 py-3 btn-error">
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
    events: Array,
});

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
const labCheck = ref(false);
const quizCheck = ref(false);
const examCheck = ref(false);
const attendanceDate = ref('');
const selectedSubject = ref('')
const selectedSemester = ref('')
const selectedSchoolYear = ref('')

// Watch `evtsAdded` and update the form dynamically
watch(
    evtsAdded,
    (newVal) => {
        returnOutputForm.events = newVal;
    },
    { deep: true } // Ensure deep watch for array changes
);


// Method to truncate text
function truncateText(text, maxLength) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}

// Extract unique subjects from the events
const uniqueSubjects = computed(() => {
    const subjects = props.events.map((event) => event.subject).filter(Boolean);
    return [...new Set(subjects)]; // Remove duplicates
});

// Extract unique subjects from the events
const uniqueSchoolYear = computed(() => {
    const subjects = props.events.map((event) => event.school_year).filter(Boolean);
    return [...new Set(subjects)]; // Remove duplicates
});

// Filtered lists for each type (singular and plural)
const quizzes = computed(() =>
    returnOutputForm.quiz = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('quiz') || event.name.toLowerCase().includes('quizzes')
    )
);

const laboratories = computed(() =>
    returnOutputForm.lab = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('lab') || event.name.toLowerCase().includes('laboratory') || event.name.toLowerCase().includes('laboratories')
    )
);

const exams = computed(() =>
    returnOutputForm.exam = evtsAdded.value.filter((event) =>
        event.name.toLowerCase().includes('exam') || event.name.toLowerCase().includes('exams')
    )
);

// Computed property for attendance_date with validation
const attendance_date = computed(() => {
    const date = attendanceDate.value;

    if (!date || !searchStartDate.value || !searchEndDate.value || !selectedSchoolYear) {
        return date;
    }

    // Check if attendanceDate is within the range
    const isWithinRange =
        new Date(date) >= new Date(searchStartDate.value) &&
        new Date(date) <= new Date(searchEndDate.value);

    if (!isWithinRange) {
        limitReached.value = true;
        maxReturnOutputMes.value =
            'Selected Attendance Date is not within the Selected Event Date Range';
        attendanceDate.value = ''
    }

    return date;
});

// Initialize form with the computed property
const returnOutputForm = useForm({
    events: [],
    quiz: [],
    lab: [],
    exam: [],
    subject: computed(() => selectedSubject.value),
    semester: computed(() => selectedSemester.value),
    school_year: computed(() => selectedSchoolYear.value),
    attendanceDate: attendance_date
});

const filteredEvents = computed(() => {
    if (!selectedSubject.value || !selectedSemester.value || !selectedSchoolYear) {
        return []; // No subject selected
    }
    let events = props.events
        .filter((event) => event.type === "return output") // Filter by event type
        .filter((event) =>
            event.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        .filter((event) => {
            if (searchStartDate.value !== "") {
                const formattedStartDate = new Date(event.start_date)
                    .toISOString()
                    .split("T")[0];
                return formattedStartDate == searchStartDate.value;
            }
            return true;
        })
        .filter((event) => {
            if (searchEndDate.value !== "") {
                const formattedEndDate = new Date(event.end_date)
                    .toISOString()
                    .split("T")[0];
                return formattedEndDate == searchEndDate.value;
            }
            return true;
        })
        .filter((event) => {
            // If no checkboxes are selected, show all events
            if (!labCheck.value && !quizCheck.value && !examCheck.value) {
                return true;
            }

            // Include event only if it matches the selected checkboxes
            const matchesLab = labCheck.value && event.name.toLowerCase().includes("lab");
            const matchesQuiz = quizCheck.value && event.name.toLowerCase().includes("quiz");
            const matchesExam = examCheck.value && event.name.toLowerCase().includes("exam");

            return matchesLab || matchesQuiz || matchesExam;
        })

        .filter((event) => {
            return (event.school_year == selectedSchoolYear.value) && (event.semester == selectedSemester.value) && (event.subject == selectedSubject.value)
        })

    // Handle no events found
    if (events.length === 0) {
        return []; // Optionally return a custom empty array or null if no events found
    }

    return events;
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

    if (event.name.toLowerCase().includes('laboratory') || event.name.toLowerCase().includes('lab') || event.name.toLowerCase().includes('laboratories')) {
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

const getFileName = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const filename = urlParams.get('filename'); // Captures ?filename=your_file_name
    return filename;
}

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
    if (selectedSubject.value != '') {
        returnOutputForm.post('/export-attendee-records/return-outputs', {
            onSuccess: () => {
                if (getFileName()) {
                    // Redirect to the GET route to download the PDF
                    const filename = getFileName();
                    window.location.href = `/download-pdf?name=${encodeURIComponent(filename)}`;
                } else {
                    console.error("Filename not returned in the response.");
                    setReturnOuputModal()
                }
            },
            onError: (errors) => {
                console.error("Error generating PDF:", errors);
                setReturnOuputModal()
            },
        });
    } else {
        limitReached.value = true;
        maxReturnOutputMes.value =
            'Select a subject first';
        evtsAdded.value = []
        numOfExamEvts.value = 0
        numOfLabEvts.value = 0
        numOfQuizEvts.value = 0
    }
};


</script>

<style scoped>
/* Add 3D effect */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
