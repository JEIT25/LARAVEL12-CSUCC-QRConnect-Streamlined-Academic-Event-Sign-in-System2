<template>
    <div class="flex flex-col items-center justify-center h-screen">
        <h2 class="text-2xl font-bold mb-4">Student Information</h2>

        <div class="mb-6">
            <p><strong>First Name:</strong> {{ props.user.fname }}</p>
            <p><strong>Last Name:</strong> {{ props.user.lname }}</p>
            <p><strong>Student ID:</strong> {{ props.user.school_id_number }}</p>
        </div>

        <div>
            <h3 class="text-xl font-bold mb-2">QR Code</h3>
            <h3 class="text-md font-bold mb-2">Take A Screenshot or Picture To Be Scanned for Attendance</h3>
            <qrcode-vue :value="qrText" :size="200" :bgColor="'#ffffff'" :fgColor="'#000000'" />
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import QrcodeVue from 'qrcode.vue';
const props = defineProps({
    user: Object,
})

const qrText = ref(props.user.school_id_number);
</script>

