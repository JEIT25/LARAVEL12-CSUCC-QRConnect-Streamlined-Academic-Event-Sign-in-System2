<template>
    <div class="flex flex-col justify-start items-center min-h-screen mt-10">
        <!-- Container -->
        <div class="flex flex-col bg-slate-900 items-center p-6 sm:p-10 rounded-lg shadow-md w-full max-w-lg">
            <!-- Smaller SVG Icon -->
            <div class="flex flex-col items-center ml-2 mt-5 sm:mt-9 mb-4 sm:mb-7">
                <img v-bind:src="page.props.profile_pict" alt="Profile Picture"
                    class="w-32 h-32 sm:w-52 sm:h-52 rounded-full shadow-lg object-cover">
            </div>

            <!-- Full Name -->
            <div class="flex justify-between items-center w-full mb-2 sm:mb-4">
                <p class="text-xs sm:text-sm uppercase tracking-wider text-gray-400">Full Name</p>
                <p class="text-white text-base sm:text-lg font-medium text-right">
                    {{ page.props.user.fname }} {{ page.props.user.middle_initial }} {{ page.props.user.lname }}
                </p>
            </div>

            <!-- Account Type -->
            <div class="flex justify-between items-center w-full mb-2 sm:mb-4">
                <p class="text-xs sm:text-sm uppercase tracking-wider text-gray-400">Account Type</p>
                <p class="text-white text-base sm:text-lg font-medium text-right">{{ page.props.user.type }}</p>
            </div>

            <!-- Birth Date -->
            <div class="flex justify-between items-center w-full mb-2 sm:mb-4">
                <p class="text-xs sm:text-sm uppercase tracking-wider text-gray-400">Birth Date</p>
                <p class="text-white text-base sm:text-lg font-medium text-right">
                    {{ new Date(page.props.user.birth_date).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }) }}
                </p>
            </div>

            <!-- Account Status -->
            <div class="flex justify-between items-center w-full mb-2 sm:mb-4">
                <p class="text-xs sm:text-sm uppercase tracking-wider text-gray-400">Account Status</p>
                <p class="text-white text-base sm:text-lg font-medium text-right">{{ page.props.user.acc_status }}</p>
            </div>

            <!-- Account Creation Date -->
            <div class="flex justify-between items-center w-full mb-4 sm:mb-6">
                <p class="text-xs sm:text-sm uppercase tracking-wider text-gray-400">Account Creation Date</p>
                <p class="text-white text-base sm:text-lg font-medium text-right">{{ formattedCreatedAt }}</p>
            </div>

            <!-- Button -->
            <div class="mt-4 sm:mt-6 mx-auto">
                <button @click="openModal"
                    class="bg-yellow-500 text-white font-bold text-sm sm:text-base py-2 px-4 rounded-md shadow hover:bg-yellow-400 transition">
                    Change Password
                </button>
            </div>
        </div>

        <!-- Modal Background -->
        <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 px-4">
            <!-- Modal Content -->
            <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg max-w-xs sm:max-w-sm w-full relative">
                <!-- Close Icon (X) -->
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 sm:h-6 w-5 sm:w-6" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-lg sm:text-xl font-semibold mb-4">Change Password</h2>

                <form @submit.prevent="submitPasswordChange">
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password"
                            class="block text-sm sm:text-base font-medium text-gray-700">Current
                            Password</label>
                        <input v-model="form.current_password" type="password" id="current_password"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm sm:text-base"
                            :class="{ 'border-red-500': form.errors.current_password }" required />
                        <div v-if="form.errors.current_password" class="text-red-500 text-xs sm:text-sm mt-1">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm sm:text-base font-medium text-gray-700">New
                            Password</label>
                        <input v-model="form.new_password" type="password" id="new_password"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm sm:text-base"
                            :class="{ 'border-red-500': form.errors.new_password }" required />
                        <div v-if="form.errors.new_password" class="text-red-500 text-xs sm:text-sm mt-1">
                            {{ form.errors.new_password }}
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="new_password_confirmation"
                            class="block text-sm sm:text-base font-medium text-gray-700">
                            Confirm New Password</label>
                        <input v-model="form.new_password_confirmation" type="password" id="new_password_confirmation"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm sm:text-base"
                            :class="{ 'border-red-500': form.errors.new_password_confirmation }" required />
                        <div v-if="form.errors.new_password_confirmation" class="text-red-500 text-xs sm:text-sm mt-1">
                            {{ form.errors.new_password_confirmation }}
                        </div>
                    </div>

                    <!-- Submit and Cancel Buttons -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="btn-success">
                            Submit
                        </button>
                        <button type="button" @click="closeModal"
                            class="ml-4 btn-error">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>



<style scoped>
#profile-container {
    width: 50%;
    height: auto;
}

/* #userDetails {
        max-width: 10%;
    } */
</style>

<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage, } from '@inertiajs/vue3';

const page = usePage();

// Modal visibility state
const isModalOpen = ref(false);

// Using useForm for form state and submission
const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

const formattedCreatedAt = computed(() => {
    const date = new Date(page.props.user.created_at);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
})

// Function to open the modal
const openModal = () => {
    isModalOpen.value = true;
};

// Function to close the modal
const closeModal = () => {
    isModalOpen.value = false;
    form.reset(); // Reset form when modal is closed
};

// Function to handle the password change submission
const submitPasswordChange = () => {
    // Submit the form using Inertia's useForm
    form.post('/change-password', {
        onSuccess: () => {
            // Close the modal on success
            closeModal();
        },
        onError: () => {
            // Handle errors if necessary
        }
    });
};
</script>