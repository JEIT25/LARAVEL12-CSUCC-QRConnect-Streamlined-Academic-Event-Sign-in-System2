<template>
    <div class="max-w-6xl mx-auto p-6 bg-white">
        <h2 class="text-2xl font-bold mb-6 text-center">Edit Event</h2>

        <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <!-- Two-column layout for the form fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Left side (Name, Description, Semester, Location) -->
                <div>
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="label">Name</label>
                        <input v-model.trim="form.name" type="text" id="name" class="input" placeholder=""
                            maxlength="20" />
                        <div class="input-error" v-if="form.errors.name">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model.trim="form.description" id="description" class="input" rows="4"
                            maxlength="50"></textarea>
                        <div class="input-error" v-if="form.errors.description">
                            {{ form.errors.description }}
                        </div>
                    </div>

                    <!-- Semester -->
                    <div class="mb-4">
                        <label for="semester" class="block text-sm font-medium text-gray-700">Semester</label>
                        <select v-model="form.semester" id="semester" class="input">
                            <option value="">Select Semester</option>
                            <option value="1st">1st Semester</option>
                            <option value="2nd">2nd Semester</option>
                        </select>
                        <div class="input-error" v-if="form.errors.semester">
                            {{ form.errors.semester }}
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="mb-4">
                        <label for="location" class="block text-sm font-medium text-gray-700">Location</label>
                        <input v-model.trim="form.location" type="text" id="location" class="input"
                            placeholder="Room, Building, etc." maxlength="50" />
                        <div class="input-error" v-if="form.errors.location">
                            {{ form.errors.location }}
                        </div>
                    </div>
                </div>

                <!-- Right side (School Year, Start Date, End Date, Subject, Subject Code, Type, Other Type) -->
                <div>
                    <!-- School Year -->
                    <div class="mb-4">
                        <label for="school_year" class="block text-sm font-medium text-gray-700">School Year</label>
                        <input v-model.trim="form.school_year" type="text" id="school_year" class="input" />
                        <div class="input-error" v-if="form.errors.school_year">
                            {{ form.errors.school_year }}
                        </div>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input v-model.trim="form.start_date" type="date" id="start_date" class="input" />
                        <div class="input-error" v-if="form.errors.start_date">
                            {{ form.errors.start_date }}
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input v-model.trim="form.end_date" type="date" id="end_date" class="input" />
                        <div class="input-error" v-if="form.errors.end_date">
                            {{ form.errors.end_date }}
                        </div>
                    </div>

                    <!-- Program -->
                    <div class="mb-4">
                        <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
                        <select v-model="form.program" id="program" class="input">
                            <option value="">Select Program</option>
                            <option value="BSIT">BSIT</option>
                            <option value="BSEE">BSEE</option>
                            <option value="BSCpE">BSCpE</option>
                            <option value="EET">EET</option>
                        </select>
                        <div class="input-error" v-if="form.errors.program">
                            {{ form.errors.program }}
                        </div>
                    </div>

                    <!-- Year -->
                    <div class="mb-4">
                        <label for="year" class="block text-sm font-medium text-gray-700">Year Level</label>
                        <select v-model="form.year_level" id="year_level" class="input">
                            <option value="">Select Year</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                        </select>
                        <div class="input-error" v-if="form.errors.year_level">
                            {{ form.errors.year_level }}
                        </div>
                    </div>

                    <!-- Subject -->
                    <div class="mb-4">
                        <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                        <input v-model.trim="form.subject" type="text" id="subject" class="input" maxlength="50" />
                        <div class="input-error" v-if="form.errors.subject">
                            {{ form.errors.subject }}
                        </div>
                    </div>

                    <!-- Subject Code -->
                    <div class="mb-4">
                        <label for="subject_code" class="block text-sm font-medium text-gray-700">Subject Code</label>
                        <input v-model.trim="form.subject_code" type="text" id="subject_code" class="input"
                            maxlength="10" />
                        <div class="input-error" v-if="form.errors.subject_code">
                            {{ form.errors.subject_code }}
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select v-model="form.type" id="type" class="input">
                            <option value="">Select Event Type</option>
                            <option value="lecture">Lecture</option>
                            <option value="class orientation">Class Orientation</option>
                            <option value="quiz">Quiz</option>
                            <option value="laboratory">Laboratory</option>
                            <option value="exam">Exam</option>
                            <option value="return output">Return Output</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="input-error" v-if="form.errors.type">
                            {{ form.errors.type }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Image (Bottom Section) -->
            <div class="mt-4">
                <label for="profile_image" class="block text-sm font-medium text-gray-700">Profile Image</label>
                <input @change="addFile" type="file" id="profile_image"
                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                <div class="input-error" v-if="form.errors.profile_image">
                    {{ form.errors.profile_image }}
                </div>
            </div>

            <!-- Submit Button (Lower Right) -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                    class="bg-gray-900 hover:bg-black text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import debounce from 'lodash/debounce'

const props = defineProps({
    event: Object,
})

let form = useForm({
    subject: props.event.subject,
    subject_code: props.event.subject_code,
    name: props.event.name,
    description: props.event.description,
    location: props.event.location,
    start_date: props.event.start_date,
    end_date: props.event.end_date, // Ensure end_date is included
    school_year: props.event.school_year,
    semester: props.event.semester || '',
    type: props.event.type || '',
    other_type: props.event.other_type || '',
    profile_image: null,
    program: props.event.program || '',
    year_level: props.event.year_level || '',
    _method: 'PUT'
})


const addFile = (event) => {
    form.profile_image = event.target.files[0]
}

const isSubmitting = ref(false)
const debouncedSubmit = debounce(() => {
    isSubmitting.value = true
    try {
        form.post(`/events/${props.event.event_id}`)
        console.log('Form submitted successfully.')
    } catch (error) {
        console.error('Error submitting form:', error)
    } finally {
        isSubmitting.value = false
    }
}, 1000)

const submitForm = () => {
    debouncedSubmit()
}
</script>
