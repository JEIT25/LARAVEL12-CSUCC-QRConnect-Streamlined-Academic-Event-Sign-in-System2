<template>
    <div class="min-h-screen"> <!-- Gray background extended to the entire screen -->
        <div class="container mx-auto px-4 py-6">
            <!-- Title -->
            <h1 class="text-3xl font-bold text-left mb-8">Active Assessment</h1>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <Link v-for="event in events" :key="event.event_id" :href="`/events/${event.event_id}`"
                    class="bg-gray-900 text-white shadow-lg rounded-lg p-6 hover:bg-gray-600 transition-colors flex flex-col justify-center">
                    <h3 class="text-xl font-bold">{{ event.name }}</h3>
                    <p class="text-gray-200 mb-5">Activity Name</p> <!-- Added label -->
                    <div class="bg-gray-700 text-white rounded p-3 mb-4"> <!-- Darker blue background for the description -->
                        <p>{{ truncateText(event.description, 100) }}</p>
                    </div>
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
import { Link } from '@inertiajs/vue3';

defineProps({
    events: Array
});

// Method to truncate text
function truncateText(text, maxLength) {
    if (text.length > maxLength) {
        return text.substring(0, maxLength) + '...';
    }
    return text;
}
</script>

<style scoped>
/* Add 3D effect */
.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>
