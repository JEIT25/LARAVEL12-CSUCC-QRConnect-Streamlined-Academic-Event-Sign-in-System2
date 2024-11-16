<template>
  <!-- Main Container -->
  <div
    class="min-h-screen bg-gradient-to-t from-blue-400 to-orange-100 bg-cover bg-center flex flex-col-reverse md:flex-row items-stretch justify-between px-4 md:px-12 lg:px-24">

    <!-- Left Section (Login Form) -->
    <div class="flex-1 flex items-start md:items-center justify-center py-5 pt-0 md:py-0">
      <div class="bg-white shadow-md rounded px-6 py-8 w-full max-w-sm">
        <h2 class="text-2xl font-bold text-center mb-6">Log in to QRConnect</h2>
        <form v-on:submit.prevent="submitForm">
          <div class="mb-4">
            <input type="text" placeholder="Email" v-model.trim="form.email"
              class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-100" />
            <p v-if="form.errors.email" class="text-red-500 mt-1">{{ form.errors.email }}</p>
          </div>
          <div class="mb-6">
            <input type="password" placeholder="Password" v-model.trim="form.password"
              class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-100" />
            <p v-if="form.errors.password" class="text-red-500 mt-1">{{ form.errors.password }}</p>
          </div>
          <button type="submit" class="btn-primary text-white rounded w-full py-2 transition duration-200">
            Login
          </button>
        </form>
      </div>
    </div>

    <!-- Right Section (Visible but Shrunk on Mobile) -->
    <div class="flex-1 flex items-center justify-center md:shrink-0 md:mt-0 p-0">
      <div class="text-center md:text-left w-full px-1 md:px-8">
        <h2 class="text-yellow-500 text-2xl md:text-5xl font-normal">
          Streamlined Event
        </h2>
        <h1 class="text-blue-900 text-2xl md:text-5xl lg:text-6xl font-bold mt-2">
          Attendance System
        </h1>
        <p class="mt-4 text-sm md:text-base text-justify text-gray-700 hidden md:block">
          A cutting-edge solution designed to simplify and optimize the process
          of attendance for various academic activities. With a focus on
          efficiency and user-friendliness, our system offers a seamless
          experience for facilitators, guests, and attendees alike.
        </p>
      </div>
    </div>

  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
const form = useForm({
  email: "",
  password: "",
});

const submitForm = () => {
  form.post('/login');
}
</script>
