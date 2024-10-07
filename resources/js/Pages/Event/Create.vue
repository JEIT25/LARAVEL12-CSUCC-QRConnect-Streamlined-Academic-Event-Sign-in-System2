<template>
  <div class="max-w-6xl mx-auto p-6 bg-white rounded-lg">
    <h2 class="text-2xl font-bold mb-6 text-left">Create New Event</h2>

    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
      <!-- Name and Location Fields in a Row -->
      <div class="flex flex-wrap -mx-2 mb-4">
        <div class="w-full sm:w-1/2 px-2">
          <label for="name" class="label font-bold">Name</label>
          <input v-model.trim="form.name" type="text" id="name" class="input" placeholder="" />
          <div class="input-error" v-if="form.errors.name">{{ form.errors.name }}</div>
        </div>
        <div class="w-full sm:w-1/2 px-2">
          <label for="location" class="block text-sm font-bold text-gray-700">Location</label>
          <input v-model.trim="form.location" type="text" id="location" class="input"
            placeholder="Room number, building name, etc." />
          <div class="input-error" v-if="form.errors.location">{{ form.errors.location }}</div>
        </div>
      </div>

      <!-- Description Field -->
      <div class="mb-4">
        <label for="description" class="block text-sm font-bold text-gray-700">Description</label>
        <textarea v-model.trim="form.description" id="description" class="input" rows="4"></textarea>
        <div class="input-error" v-if="form.errors.description">{{ form.errors.description }}</div>
      </div>

      <!-- Start Date, End Date, and School Year Fields -->
      <div class="flex flex-wrap -mx-2 mb-4">
        <div class="w-full sm:w-1/2 px-2">
          <label for="start_date" class="block text-sm font-bold text-gray-700">Start Date</label>
          <input v-model.trim="form.start_date" type="date" id="start_date" class="input" />
          <div class="input-error" v-if="form.errors.start_date">{{ form.errors.start_date }}</div>
        </div>
        <div class="w-full sm:w-1/2 px-2">
          <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
          <input v-model.trim="form.end_date" type="date" id="end_date" class="input" />
          <div class="input-error" v-if="form.errors.end_date">{{ form.errors.end_date }}</div>
        </div>
      </div>

      <div class="flex flex-wrap -mx-2 mb-4">
        <div class="w-full sm:w-1/2 px-2">
          <label for="school_year" class="block text-sm font-bold text-gray-700">School Year (Optional)</label>
          <input v-model.trim="form.school_year" type="text" id="school_year" class="input"
            placeholder="e.g., 2023-2024" />
          <div class="input-error" v-if="form.errors.school_year">{{ form.errors.school_year }}</div>
        </div>
      </div>

      <!-- Semester and Type Fields -->
      <div class="flex flex-wrap -mx-2 mb-4">
        <div class="w-full sm:w-1/2 px-2">
          <label for="semester" class="block text-sm font-bold text-gray-700">Semester (Optional)</label>
          <select v-model="form.semester" id="semester" class="input">
            <option value="">Select Semester</option>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
          </select>
          <div class="input-error" v-if="form.errors.semester">{{ form.errors.semester }}</div>
        </div>
        <div class="w-full sm:w-1/2 px-2">
          <label for="type" class="block text-sm font-bold text-gray-700">Type</label>
          <select v-model="form.type" id="type" class="input">
            <option value="">Select Event Type</option>
            <option value="class attendance">Class Attendance</option>
            <option value="class orientation">Class Orientation</option>
            <option value="exam">Exam</option>
            <option value="other">Other</option>
          </select>
          <div class="input-error" v-if="form.errors.type">{{ form.errors.type }}</div>
        </div>
      </div>

      <!-- Other Type Field (only shown if type is "other") -->
      <div v-if="form.type === 'other'" class="mb-4">
        <label for="other_type" class="block text-sm font-bold text-gray-700">Specify Other Type</label>
        <input v-model.trim="form.other_type" type="text" id="other_type" class="input" placeholder="Specify type" />
        <div class="input-error" v-if="form.errors.other_type">{{ form.errors.other_type }}</div>
      </div>

      <!-- Subject and Subject Code Fields -->
      <div class="flex flex-wrap -mx-2 mb-4">
        <div class="w-full sm:w-1/2 px-2">
          <label for="subject" class="block text-sm font-bold text-gray-700">Subject (Optional)</label>
          <input v-model.trim="form.subject" type="text" id="subject" class="input" placeholder="Enter subject" />
          <div class="input-error" v-if="form.errors.subject">{{ form.errors.subject }}</div>
        </div>
        <div class="w-full sm:w-1/2 px-2">
          <label for="subject_code" class="block text-sm font-bold text-gray-700">Subject Code (Optional)</label>
          <input v-model.trim="form.subject_code" type="text" id="subject_code" class="input"
            placeholder="Enter subject code" />
          <div class="input-error" v-if="form.errors.subject_code">{{ form.errors.subject_code }}</div>
        </div>
      </div>

      <!-- Profile Image Field -->
      <div class="mb-4">
        <label for="profile_image" class="block text-sm font-bold text-gray-700">Profile Image (Optional)</label>
        <input @change="handleFileUpload" type="file" id="profile_image"
          class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
        <div class="input-error" v-if="form.errors.profile_image">{{ form.errors.profile_image }}</div>
      </div>

      <!-- Submit Button -->
      <div class="flex justify-end mt-6">
        <button type="submit"
          class="w-48 bg-amber-500 hover:bg-blue-950 text-gray font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
          Submit
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

const form = useForm({
  name: '',
  description: '',
  location: '',
  start_date: '',
  end_date: '',
  school_year: '',
  semester: '',
  profile_image: null,
  type: 'class attendance',
  other_type: '',
  subject: '',
  subject_code: '',
})

const handleFileUpload = (event) => {
  form.profile_image = event.target.files[0]
}

const isSubmitting = ref(false)

const debouncedSubmit = debounce(() => {
  isSubmitting.value = true
  try {
    form.post('/events')
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