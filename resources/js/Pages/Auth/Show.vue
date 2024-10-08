<template>
    <div class="flex flex-col justify-center items-center min-h-screen px-4">
        <!-- Container -->
        <div class="flex flex-col items-center max-w-xl bg-slate-800 p-10 rounded-lg shadow-md">
            <!-- Smaller SVG Icon -->
            <div class="flex flex-col items-center ml-2 mt-9 mb-7 p-10">
                <svg fill="#ffd333" height="100px" width="100px" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 512 512">
                    <path
                        d="M179.47,101.914c1.76,1.081,3.706,1.597,5.632,1.597c3.618,0,7.152-1.822,9.185-5.131c1.032-1.677,2.129-3.324,3.26-4.892 c10.237-14.178,24.463-23.661,41.14-27.424c5.803-1.31,9.444-7.075,8.136-12.877c-1.311-5.802-7.078-9.446-12.877-8.136c-21.878,4.937-40.504,17.326-53.864,35.829 c-1.443,1.998-2.838,4.09-4.145,6.218C172.821,92.165,174.404,98.798,179.47,101.914z" />
                    <path
                        d="M266.48,64.856c0.044,0.006,0.151,0.021,0.176,0.024c0.467,0.06,0.928,0.089,1.387,0.089c5.314,0,9.895-3.945,10.614-9.358 c0.782-5.879-3.388-11.291-9.267-12.097c-0.179-0.024-0.355-0.046-0.532-0.066c-5.917-0.625-11.216,3.668-11.838,9.583 C256.401,58.906,260.627,64.173,266.48,64.856z" />
                    <path
                        d="M477.421,343.06c-24.938-6.834-51.074-17.918-73.591-31.213c-3.378-1.994-7.572-1.995-10.95,0c-3.454,2.039-6.921,3.988-10.403,5.871 c-18.551-1.1-31.726-6.061-40.95-24.353c22.307-15.171,30.314-27.458,39.293-82.002c0.56-3.401,1.403-7.8,2.381-12.892 c5.367-27.983,13.48-70.272,5.49-97.014c-9.253-30.978-26.81-56.828-50.777-74.755C314.563,9.235,286.237,0.002,255.999,0c-30.238,0-58.562,9.232-81.914,26.699 c-23.967,17.928-41.526,43.777-50.781,74.756c-7.987,26.734,0.124,69.02,5.491,97.001c0.978,5.099,1.821,9.501,2.382,12.906 c9.03,54.841,17.077,66.966,39.661,82.254c-7.189,13.938-23.255,20.769-47.132,29.948c-21.353,8.209-45.553,17.513-64.549,37.182 c-21.871,22.647-32.502,54.649-32.502,97.835c0,5.948,4.822,10.77,10.77,10.77h218.573h80.129c4.165,5.519,8.471,10.284,12.587,14.317c17.86,17.5,39.674,28.332,49.639,28.332 c9.968,0,31.78-10.833,49.64-28.332c17.039-16.695,37.35-45.826,37.35-89.808v-40.412C485.345,348.595,482.101,344.342,477.421,343.06z M143.944,107.62c15.863-53.096,58.8-86.08,112.054-86.08c53.261,0.004,96.198,32.988,112.055,86.081c2.882,9.645,2.935,23.058,1.696,37.119 c-23.512-7.957-36.123-39.86-36.257-40.205c-1.275-3.342-4.126-5.833-7.609-6.647c-3.482-0.812-7.143,0.154-9.768,2.583c-50.444,46.68-138.744,19.165-139.619,18.885 c-4.767-1.536-9.96,0.417-12.537,4.71c-3.803,6.338-13.099,12.648-21.981,17.317C141.037,128.558,141.292,116.493,143.944,107.62z M152.432,207.863c-0.606-3.683-1.475-8.216-2.481-13.465c-1.648-8.588-3.686-19.219-5.349-30.333c9.033-4.072,23.313-11.628,32.729-22.1 c22.718,5.864,91.819,19.382,142.358-16.687c7.919,14.053,23.411,35.121,47.352,41.135c-1.594,10.262-3.461,20.014-4.993,27.998 c-1.006,5.242-1.874,9.77-2.48,13.451c-8.945,54.336-14.584,57.923-38.322,73.02c-3.97,2.525-8.457,5.379-13.454,8.759 c-17.203,9.87-34.628,14.872-51.795,14.872c-17.167-0.001-34.59-5.003-51.789-14.868c-4.994-3.38-9.481-6.234-13.449-8.757 c-0.34-0.215-0.673-0.429-1.005-0.64c-0.023-0.014-0.046-0.029-0.069-0.043C166.846,265.669,161.243,261.373,152.432,207.863z M255.999,447.809v0.001H48.448c3.548-73.595,43.903-89.116,82.987-104.142c22.591-8.685,45.82-17.623,57.54-38.296 c1.133,0.75,2.289,1.522,3.473,2.324l0.66,0.414c20.621,11.906,41.779,17.943,62.887,17.944c0,0,0.002,0,0.003,0 c21.106,0,42.266-6.039,62.893-17.948l0.659-0.414c1.314-0.891,2.59-1.742,3.842-2.568c7.582,14.025,17.243,22.352,27.72,27.281 c-10.335,4.121-20.905,7.662-31.822,10.653c-4.68,1.282-7.924,5.535-7.924,10.388v40.412c0,3.885,0.166,7.648,0.467,11.302h-67.02c-5.948,0-10.77,4.822-10.77,10.77s4.822,10.77,10.77,10.77h70.648c1.994,7.139,3.148,13.461,3.778,18.818c0.021,0.207,0.046,0.415,0.067,0.621 c0.018,0.195,0.026,0.392,0.036,0.586c0.015,0.424,0.025,0.832,0.025,1.215c0,7.732-1.365,15.121-3.961,21.446 c-1.857,4.076-6.086,6.507-10.563,6.507c-3.097,0-6.156-1.332-8.226-3.726c-7.773-9.382-18.758-14.806-28.189-14.806c-9.437,0-19.415,5.424-27.192,14.806c-2.043,2.395-5.084,3.726-8.178,3.726c-4.476,0-8.705-2.431-10.563-6.507 c-2.596-6.325-3.961-13.714-3.961-21.446c0-7.345,0.055-13.594,0.085-18.639C254.379,448.786,255.999,447.809,255.999,447.809z" />
                </svg>
            </div>

            <!-- User Info -->
            <div class="text-center text-yellow-300 p-10">
                <p class="text-white text-xl font-semibold">{{ page.props.user.fname }} {{
                    page.props.user.middle_initial }} {{ page.props.user.lname }}</p>
                <p class="text-xs uppercase tracking-wider">{{ page.props.user.type }}</p>
            </div>

            <!-- Button -->
            <div class="mt-6">
                <button @click="openModal"
                    class="bg-yellow-500 text-white py-2 px-4 rounded-md shadow hover:bg-yellow-400 transition">
                    Change Password
                </button>
            </div>
        </div>

        <!-- Modal Background -->
        <div v-if="isModalOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
            <!-- Modal Content -->
            <div class="bg-white p-6 rounded-lg shadow-lg max-w-sm w-full relative">
                <!-- Close Icon (X) -->
                <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                <h2 class="text-xl font-semibold mb-4">Change Password</h2>

                <form @submit.prevent="submitPasswordChange">
                    <!-- Current Password -->
                    <div class="mb-4">
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current
                            Password</label>
                        <input v-model="form.current_password" type="password" id="current_password"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            :class="{ 'border-red-500': form.errors.current_password }" required />
                        <!-- Display error for current password -->
                        <div v-if="form.errors.current_password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.current_password }}
                        </div>
                    </div>

                    <!-- New Password -->
                    <div class="mb-4">
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input v-model="form.new_password" type="password" id="new_password"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            :class="{ 'border-red-500': form.errors.new_password }" required />
                        <!-- Display error for new password -->
                        <div v-if="form.errors.new_password" class="text-red-500 text-sm mt-1">
                            {{ form.errors.new_password }}
                        </div>
                    </div>

                    <!-- Confirm New Password -->
                    <div class="mb-4">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                            New Password</label>
                        <input v-model="form.new_password_confirmation" type="password" id="new_password_confirmation"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                            :class="{ 'border-red-500': form.errors.new_password_confirmation }" required />
                        <!-- Display error for password confirmation -->
                        <div v-if="form.errors.new_password_confirmation" class="text-red-500 text-sm mt-1">
                            {{ form.errors.new_password_confirmation }}
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-yellow-500 text-white py-2 px-4 rounded-md shadow hover:bg-yellow-400 transition">
                            Submit
                        </button>
                        <button type="button" @click="closeModal"
                            class="ml-4 bg-gray-500 text-white py-2 px-4 rounded-md shadow hover:bg-gray-400 transition">
                            Cancel
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

// Modal visibility state
const isModalOpen = ref(false);

// Using useForm for form state and submission
const form = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: ''
});

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