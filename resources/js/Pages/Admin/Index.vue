<template>
    <div class="h-screen bg-cover bg-center flex items-center justify-center"
         :style="{ backgroundImage: `url(${coloredBackgroundImage})` }">
        <div class="bg-white bg-opacity-80 p-8 rounded-lg">
            <!-- Image Section -->
            <div class="mb-6">
                <img :src="qrBackground" alt="QR Code Animation" class="w-48 mx-auto" />
            </div>

            <!-- Text Section -->
            <h2 class="text-yellow-500 text-3xl font-normal">WELCOME TO ADMINISTRATOR PANEL!</h2>
            <h1 class="text-blue-900 text-5xl font-bold mt-2">QRConnect</h1>
            <p class="mt-4 text-sm text-gray-700">
                A cutting-edge solution designed to simplify and optimize attendance for various academic activities. <br> This panel is subject to further improvements.
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

<style scoped>
/* Add any additional styles you want here */
</style>


<script setup>
</script>