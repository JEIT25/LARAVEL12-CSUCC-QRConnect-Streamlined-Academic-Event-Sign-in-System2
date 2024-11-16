<template>
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-lg border border-gray-200">
        <h1 class="text-2xl font-semibold mb-4">Welcome, {{ facilitator.fname }} {{ facilitator.lname }}</h1>
        <p class="mb-2 text-gray-700">Email: {{ facilitator.email }}</p>
        <p class="mb-4 text-gray-700">Program: {{ facilitator.program }}</p>

        <div class="border-t border-gray-300 mt-4 pt-4">
            <button
                class="w-full py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                @click="forgotPassword">
                Forgot Password
            </button>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    facilitator: Object,
});

const forgotPassword = () => {
    // Logic for forgot password
    console.log("Forgot password clicked");
};
</script>

<style scoped>
/* Optional custom styles if necessary */
</style>
