<template>
    <div class="h-screen bg-cover bg-center flex items-center justify-center px-4 sm:px-8"
        :style="{ backgroundImage: `url(${coloredBackgroundImage})` }">
        <div class="bg-white bg-opacity-80 p-6 sm:p-8 rounded-lg w-full max-w-md sm:max-w-lg">
            <!-- Image Section -->
            <div class="mb-4 sm:mb-6">
                <img :src="qrBackground" alt="QR Code Animation" class="w-32 sm:w-48 mx-auto" />
            </div>

            <!-- Text Section -->
            <h2 class="text-yellow-500 text-xl sm:text-3xl font-normal text-center">WELCOME TO THE ADMINS PANEL!
            </h2>
            <h1 class="text-blue-900 text-3xl sm:text-5xl font-bold mt-2 text-center">QRConnect</h1>
            <p class="mt-3 sm:mt-4 text-xs sm:text-sm text-gray-700 text-center">
                A cutting-edge solution designed to simplify and optimize attendance for various academic activities.
            </p>
            <p class="mt-0 text-xs sm:text-sm text-gray-700 text-center leading-relaxed">
                This panel is subject to further improvements.
            </p>
        </div>
    </div>
</template>


<script setup>
const props = defineProps({
    qrBackground: String,
    coloredBackgroundImage: String,
});
</script>
