<template>
  <div class="flex items-center justify-center h-screen bg-gray-100">
    <div class="bg-white shadow-md rounded px-8 py-6 max-w-sm w-full">
      <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
      <form v-on:submit.prevent="submitForm">
        <div class="mb-4">
          <input
            type="text"
            placeholder="Email"
            v-model.trim="form.email"
            class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
          <p v-if="form.errors.email" class="text-red-500 mt-1">{{ form.errors.email }}</p>
        </div>
        <div class="mb-6">
          <input
            type="password"
            placeholder="Password"
            v-model.trim="form.password"
            class="border border-gray-300 rounded w-full py-2 px-3 focus:outline-none focus:ring-2 focus:ring-blue-100"
          />
          <p v-if="form.errors.password" class="text-red-500 mt-1">{{ form.errors.password }}</p>
        </div>
        <button
          type="submit"
          class="btn-primary text-white rounded w-full py-2 transition duration-200"
        >
          Login
        </button>
      </form>
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