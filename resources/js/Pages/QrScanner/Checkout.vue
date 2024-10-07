<template>
    <div class="flex flex-col md:flex-row h-screen">
        <!-- Left side: QR scanner -->
        <div class="md:w-1/2 flex justify-center items-center border-r-4 border-blue-500 p-4">
            <div class="flex flex-col items-center">
                <h2 class="text-2xl font-bold mb-4 text-center">Attendance Check Out for Event {{ props.event.name }}
                </h2>
                <div id="reader" class="w-full max-w-md h-64 bg-gray-200"></div>
            </div>
        </div>

        <!-- Right side: Scan result and attendee_record info -->
        <div class="md:w-1/2 p-5 flex flex-col justify-center items-center">
            <div class="w-full max-w-md">
                <h1 class="text-2xl font-bold mb-4">SCAN RESULT</h1>

                <div v-if="attendee_record" class="mt-4 bg-white p-4 rounded-lg shadow-md">
                    <div class="text-center">
                        <h5 class="text-lg font-semibold mb-2">Attendee Information</h5>
                        <h1 :class="status ? 'bg-green-500' : 'bg-red-500'" v-if="message">
                            {{ message }}
                        </h1>
                        <p><strong>Name:</strong> {{ attendee_record.full_name }}</p>
                        <p><strong>Unique ID:</strong> {{ attendee_record.unique_id }}</p>
                        <p v-if="attendee_record.checkout"><strong>Check-out Time:</strong> {{
                            convertToLocalDateTime(attendee_record.checkout) }}</p>
                        <p v-else><strong>Check-out Time:</strong> {{ convertToLocalDateTime(checkout) }}</p>
                    </div>
                </div>

                <div v-else class="text-lg text-center">
                    <h1 :class="status ? 'bg-green-500' : 'bg-red-500'" v-if="message">
                        {{ message }}
                    </h1>
                    <p v-else>No attendee record found yet.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio element for success sound -->
    <audio id="successSound">
        <source src="../../scanner/scan-sound.mp3" type="audio/mpeg">
    </audio>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios'; // Axios for handling HTTP requests
import { Html5QrcodeScanner } from 'html5-qrcode';

// Reactive variables
const scannedData = ref(null);
const scannerEnabled = ref(true);
const attendee_record = ref(null); // This will hold the attendee_record info
const message = ref(null);
const status = ref(null);
const checkout = ref(null); // In case of an existing attendee_record, this will be set with checkout datetime value of the existing attendee_record
const props = defineProps({
    event: Object
});

function convertToLocalDateTime(isoDate) { // Function to format the datetime value of checkout
    // Replace the space between date and time with 'T' to make it ISO 8601 compliant
    const formattedDate = isoDate.replace(' ', 'T');
    const date = new Date(formattedDate);

    if (isNaN(date.getTime())) {
        return 'Invalid Date'; // Handle invalid date
    }

    return date.toLocaleString(); // Formats to local date and time
}

onMounted(() => {
    const onScanSuccess = (qrCodeMessage) => {
        if (!scannerEnabled.value) return;
        scannedData.value = qrCodeMessage;
        document.getElementById('successSound').play();

        // Send the scanned QR data to the backend
        axios.post(`/events/${props.event.event_id}/qrscanner/checkout`, {
            qrData: qrCodeMessage,
        })
            .then(response => {
                // Update the attendee_record information
                attendee_record.value = response.data.attendee_record; // Ensure `full_name` and `unique_id` are part of the response
                message.value = response.data.message;
                checkout.value = response.data.check_out;
                status.value = response.data.status;
            })
            .catch(error => {
                console.error('Error submitting QR code:', error);
            });

        // Disable scanner briefly to avoid duplicate scans
        scannerEnabled.value = false;
        setTimeout(() => {
            scannerEnabled.value = true;
        }, 1000);
    };

    const onScanError = (errorMessage) => {
        console.log(errorMessage);
    };

    const html5QrcodeScanner = new Html5QrcodeScanner('reader', {
        fps: 10,
        qrbox: 250,
        useBarCodeDetectorIfSupported: true,
        willReadFrequently: true,
        showZoomSliderIfSupported: true,
        defaultZoomValueIfSupported: 5,
        rememberLastUsedCamera: true,
        showTorchButtonIfSupported: true
    });
    html5QrcodeScanner.render(onScanSuccess, onScanError);
});
</script>
