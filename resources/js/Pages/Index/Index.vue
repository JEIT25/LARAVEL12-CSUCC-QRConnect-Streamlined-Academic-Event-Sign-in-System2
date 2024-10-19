<template>
    <div class="min-h-screen bg-cover bg-center bg-no-repeat flex items-center justify-center md:px-12 lg:px-24"
        :style="{ backgroundImage: `url(${coloredBackgroundImage})` }">
        <div class="flex flex-col md:flex-row items-center justify-between w-full max-w-6xl mb-10">
            <!-- Image Section -->
            <div class="flex justify-center md:justify-start w-full md:w-1/2 mb-8 md:mb-0">
                <img :src="qrBackground" alt="QR Code Animation" class="max-w-full w-64 md:w-96" />
            </div>

            <!-- Text Section -->
            <div class="text-center md:text-left w-full md:w-1/2">
                <h2 class="text-yellow-500 text-3xl md:text-5xl font-normal">
                    Streamlined Student
                </h2>
                <h1 class="text-blue-900 text-4xl md:text-6xl lg:text-7xl font-bold mt-4 md:mt-2">
                    Sign-in System
                </h1>
                <p class="mt-4 text-sm text-justify md:text-base p-6 md:p-0 text-white md:text-gray-700">
                    A cutting-edge solution designed to simplify and optimize the process
                    of attendance for various academic activities. With a focus on
                    efficiency and user-friendliness, our system offers a seamless
                    experience for facilitators, and attendeees alike.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>


const props = defineProps({
    qrBackground: String,
    qrGif: String,
    coloredBackgroundImage: String,
});
</script>
