<template>
  <div class="max-w-6xl mx-auto p-6 bg-white rounded-lg mt-10 border">
    <h2 class="text-2xl font-bold mb-6 text-left">Create New Event</h2>

    <form @submit.prevent="submitForm" enctype="multipart/form-data" class="space-y-4">
      <!-- Step 1: Event Type and Additional Fields -->
      <div v-if="step === 1">
        <div class="flex flex-wrap -mx-2 mb-4">
          <div class="w-full sm:w-1/2 px-2">
            <label for="type" class="label font-bold">What Type of Event?</label>
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
        <div v-if="form.type === 'other'" class="mb-4">
          <label for="other_type" class="label font-bold">Specify Other Type</label>
          <input v-model.trim="form.other_type" type="text" id="other_type" class="input" />
          <div class="input-error" v-if="form.errors.other_type">{{ form.errors.other_type }}</div>
        </div>

        <div v-if="form.type == 'exam' || form.type == 'class attendance' || form.type == 'class orientation'">
          <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full sm:w-1/2 px-2">
              <label for="subject" class="label font-bold">Subject</label>
              <input v-model.trim="form.subject" type="text" id="subject" class="input" />
              <div class="input-error" v-if="form.errors.subject">{{ form.errors.subject }}</div>
            </div>
            <div class="w-full sm:w-1/2 px-2">
              <label for="subject_code" class="label font-bold">Subject Code</label>
              <input v-model.trim="form.subject_code" type="text" id="subject_code" class="input" />
              <div class="input-error" v-if="form.errors.subject_code">{{ form.errors.subject_code }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 2: Basic Information -->
      <div v-if="step === 2">
        <!-- Name and Location Fields in a Row -->
        <div class="flex flex-wrap -mx-2 mb-4">
          <div class="w-full sm:w-1/2 px-2">
            <label for="name" class="label font-bold">Name</label>
            <input v-model.trim="form.name" type="text" id="name" class="input" />
            <div class="input-error" v-if="form.errors.name">{{ form.errors.name }}</div>
          </div>
          <div class="w-full sm:w-1/2 px-2">
            <label for="location" class="label font-bold">Location</label>
            <input v-model.trim="form.location" type="text" id="location" class="input" />
            <div class="input-error" v-if="form.errors.location">{{ form.errors.location }}</div>
          </div>
        </div>
        <div class="mb-4">
          <label for="description" class="label font-bold">Description</label>
          <textarea v-model.trim="form.description" id="description" class="input" rows="4"></textarea>
          <div class="input-error" v-if="form.errors.description">{{ form.errors.description }}</div>
        </div>
      </div>

      <!-- Step 3: Date and School Information -->
      <div v-if="step === 3">
        <div class="flex flex-wrap -mx-2 mb-4">
          <div class="w-full sm:w-1/2 px-2">
            <label for="start_date" class="label font-bold">Start Date</label>
            <input v-model.trim="form.start_date" type="date" id="start_date" class="input" />
            <div class="input-error" v-if="form.errors.start_date">{{ form.errors.start_date }}</div>
          </div>
          <div class="w-full sm:w-1/2 px-2">
            <label for="end_date" class="label font-bold">End Date</label>
            <input v-model.trim="form.end_date" type="date" id="end_date" class="input" />
            <div class="input-error" v-if="form.errors.end_date">{{ form.errors.end_date }}</div>
          </div>
        </div>
        <div class="flex flex-wrap -mx-2 mb-4">
          <div class="w-full sm:w-1/2 px-2">
            <label for="school_year" class="label font-bold">School Year</label>
            <input v-model.trim="form.school_year" type="text" id="school_year" class="input"
              placeholder="e.g., 2023-2024" />
            <div class="input-error" v-if="form.errors.school_year">{{ form.errors.school_year }}</div>
          </div>

          <div class="w-full sm:w-1/2 px-2"
            v-if="form.type == 'exam' || form.type == 'class attendance' || form.type == 'class orientation'">
            <label for="semester" class="block text-sm font-bold text-gray-700">Semester</label>
            <select v-model="form.semester" id="semester" class="input">
              <option value="">Select Semester</option>
              <option value="1st">1st</option>
              <option value="2nd">2nd</option>
            </select>
            <div class="input-error" v-if="form.errors.semester">{{ form.errors.semester }}</div>
          </div>
        </div>
      </div>

      <!-- Step 4: Profile Image -->
      <div v-if="step === 4">
        <div class="mb-4">
          <label for="profile_image" class="label font-bold">Profile Image (Optional)</label>
          <!-- Styled input for file upload -->
          <input @change="handleFileUpload" type="file" id="profile_image"
            class="w-full borde text-black font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline cursor-pointer" />
          <div class="input-error" v-if="form.errors.profile_image">{{ form.errors.profile_image }}</div>
        </div>
      </div>

      <!-- Step Navigation Buttons -->
      <div class="flex justify-between mt-6">
        <button v-if="step > 1" type="button" @click="previousStep" class="btn-primary">Previous</button>
        <button v-if="step < 4" type="button" @click="nextStep" class="btn-primary">Next</button>
        <button v-if="step === 4" type="submit"
          class="w-48 bg-amber-500 hover:bg-blue-950 text-gray font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Submit</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const step = ref(1)

const form = useForm({
  name: '',
  description: '',
  location: '',
  start_date: '',
  end_date: '',
  school_year: '',
  semester: '',
  profile_image: null,
  type: '',
  other_type: '',
  subject: '',
  subject_code: '',
})

const nextStep = () => {
  if (step.value < 4) {
    step.value++
  }
}

const previousStep = () => {
  if (step.value > 1) {
    step.value--
  }
}

const handleFileUpload = (event) => {
  form.profile_image = event.target.files[0]
}

const submitForm = () => {
  form.post('/events', {
    onFinish: () => step.value = 1 // Reset the form step to the first page after successful submission
  })
}
</script>