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

          <!-- Specify Other Type if 'Other' is selected -->
          <div v-if="form.type === 'other'" class="w-full sm:w-1/2 px-2">
            <label for="other_type" class="label font-bold">Specify Other Type</label>
            <input v-model.trim="form.other_type" type="text" id="other_type" class="input" maxlength="20" />
            <div class="input-error" v-if="form.errors.other_type">{{ form.errors.other_type }}</div>
          </div>
        </div>

        <!-- Radio for Student-only events (only when 'Other' is selected) -->
        <div v-if="form.type === 'other'" class="mb-4">
          <label class="label font-bold">Is this event attended by students only?</label>
          <div class="flex space-x-4">
            <label>
              <input type="radio" v-model="isStudentOnly" value="yes" /> Yes
            </label>
            <label>
              <input type="radio" v-model="isStudentOnly" value="no" /> No
            </label>
          </div>
        </div>

        <!-- Subject, Subject Code, Program, Year Level (for specific types or student-only events) -->
        <div
          v-if="form.type == 'exam' || form.type == 'class attendance' || form.type == 'class orientation' || isStudentOnly === 'yes'">
          <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full sm:w-1/2 px-2">
              <label for="subject" class="label font-bold">Subject</label>
              <input v-model.trim="form.subject" type="text" id="subject" class="input" maxlength="50" />
              <div class="input-error" v-if="form.errors.subject">{{ form.errors.subject }}</div>
            </div>
            <div class="w-full sm:w-1/2 px-2">
              <label for="subject_code" class="label font-bold">Subject Code</label>
              <input v-model.trim="form.subject_code" type="text" id="subject_code" class="input" maxlength="10" />
              <div class="input-error" v-if="form.errors.subject_code">{{ form.errors.subject_code }}</div>
            </div>
          </div>

          <!-- Year Level and Program -->
          <div class="flex flex-wrap -mx-2 mb-4">
            <div class="w-full sm:w-1/2 px-2">
              <label for="year_level" class="label font-bold">Year Level</label>
              <select v-model="form.year_level" id="year_level" class="input">
                <option value="">Select Year</option>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
              </select>
              <div class="input-error" v-if="form.errors.year_level">{{ form.errors.year_level }}</div>
            </div>
            <div class="w-full sm:w-1/2 px-2">
              <label for="program" class="label font-bold">Program</label>
              <select v-model="form.program" id="program" class="input">
                <option value="">Select Program</option>
                <option value="BSIT">BSIT</option>
                <option value="BSEE">BSEE</option>
                <option value="BSCpE">BSCpE</option>
                <option value="EET">EET</option>
              </select>
              <div class="input-error" v-if="form.errors.program">{{ form.errors.program }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Step 2: Basic Information -->
      <div v-if="step === 2">
        <div class="flex flex-wrap -mx-2 mb-4">
          <div class="w-full sm:w-1/2 px-2">
            <label for="name" class="label font-bold">Name</label>
            <input v-model.trim="form.name" type="text" id="name" class="input" maxlength="30" />
            <div class="input-error" v-if="form.errors.name">{{ form.errors.name }}</div>
          </div>
          <div class="w-full sm:w-1/2 px-2">
            <label for="location" class="label font-bold">Location</label>
            <input v-model.trim="form.location" type="text" id="location" class="input" maxlength="50" />
            <div class="input-error" v-if="form.errors.location">{{ form.errors.location }}</div>
          </div>
        </div>
        <div class="mb-4">
          <label for="description" class="label font-bold">Description</label>
          <textarea v-model.trim="form.description" id="description" class="input" rows="4" maxlength="100"></textarea>
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
          <div class="w-full sm:w-1/2 px-2">
            <label for="semester" class="block text-sm font-bold">Semester</label>
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
          <input @change="handleFileUpload" type="file" id="profile_image"
            class="w-full borde text-black font-bold py-2 px-4 rounded cursor-pointer" />
          <div class="input-error" v-if="form.errors.profile_image">{{ form.errors.profile_image }}</div>
        </div>
      </div>

      <!-- Step Navigation Buttons -->
      <div class="flex justify-between mt-6">
        <button v-if="step > 1" type="button" @click="previousStep" class="btn-primary">Previous</button>
        <button v-if="step < 4" type="button" @click="nextStep" class="btn-primary">Next</button>
        <button v-if="step === 4" type="submit"
          class="w-48 bg-amber-500 hover:bg-blue-950 hover:text-white text-gray font-bold py-2 px-4 rounded cursor-pointer">Submit</button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const step = ref(1);
const isStudentOnly = ref('no'); // Set default value for radio button

const form = useForm({
  type: '',
  other_type: '',
  subject: '',
  subject_code: '',
  program: '',
  year_level: '',
  name: '',
  location: '',
  description: '',
  start_date: '',
  end_date: '',
  school_year: '',
  semester: '',
  profile_image: null,
});

const nextStep = () => {
  step.value++;
};

const previousStep = () => {
  step.value--;
};

const handleFileUpload = (event) => {
  const file = event.target.files[0];
  form.profile_image = file;
};

const submitForm = () => {
  form.post('/events/', {
    onSuccess: () => {
      step.value = 1;
      isStudentOnly.value = 'no';
    },
  });
};
</script>