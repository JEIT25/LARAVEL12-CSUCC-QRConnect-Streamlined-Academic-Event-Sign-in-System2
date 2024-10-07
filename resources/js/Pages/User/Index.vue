<template>
    <div class="container mx-auto p-4">
        <!-- Header and Create Button -->
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Facilitator Accounts</h1>
            <!-- Inertia Link to Create Facilitator Account -->
            <Link class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                href="/users/create">
            Create Facilitator Account
            </Link>
        </div>

        <!-- Facilitator List -->
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-100 text-center">
                        <th class="py-2 px-4">Full Name</th>
                        <th class="py-2 px-4">Account Status</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="facilitator in facilitator_accs" :key="facilitator.id"
                        class="border-t border-gray-200 text-center">
                        <!-- Full Name -->
                        <td class="py-2 px-4">{{ facilitator.fname }} {{ facilitator.lname }}</td>

                        <!-- Account Status -->
                        <td class="py-2 px-4">
                            <span v-if="facilitator.acc_status === 'active'" class="text-green-500">Active</span>
                            <span v-else class="text-red-500">Disabled</span>
                        </td>

                        <!-- Action Buttons -->
                        <td class="py-2 px-4">
                            <!-- Activate/Disable Button -->
                            <Link v-if="facilitator.acc_status === 'disabled'"
                                :href="`/users/${facilitator.user_id}/active`"
                                class="bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded mr-2" as="button"
                                method="post">
                            Activate
                            </Link>

                            <Link v-else :href="`/users/${facilitator.user_id}/disabled`"
                                class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded mr-2"
                                as="button"
                                method="post">
                            Disable
                            </Link>

                            <!-- Delete Button -->
                            <Link :href="`/users/${facilitator.user_id}`"
                                class="bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded"
                                as="button" method="delete">
                            Delete
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

// Get the list of facilitator_accs as a prop passed from Laravel backend
const props = defineProps({
    facilitator_accs: Array
});
</script>
