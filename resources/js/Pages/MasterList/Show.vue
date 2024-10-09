<template>
    <div class="container mx-auto px-4 py-6">
        <div class="bg-gray-100 shadow-lg rounded-lg p-6">
            <!-- Master List Header -->
            <div class="flex items-center justify-center mb-4">
                <h1 class="text-2xl font-bold">{{ props.master_list.name }}</h1>
                <!-- Delete Master List Button -->
                <Link :href="`/events/${props.master_list.event_id}/master-lists/${props.master_list.master_list_id}`"
                    as="button" method="delete" class="text-red-500 text-right hover:text-red-700 ml-52">
                Delete Master List
                </Link>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Unique ID</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in props.master_list_members" :key="member.master_list_member_id">
                            <td class="py-2 px-4 border-b text-center">{{ member.full_name }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ member.unique_id }}</td>
                            <td class="py-2 px-4 border-b text-center">
                                <Link class="bg-red-500 text-white hover:bg-red-600 px-2 py-1 rounded"
                                    :href="`/master-list-members/${member.master_list_member_id}`" as="button"
                                    method="delete">
                                Delete
                                </Link>
                                <button @click="showQRCode(member.unique_id, member.full_name)"
                                    class="btn-primary text-white px-2 py-1 rounded ml-2">
                                    QR Code
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add Student Buttons (aligned to the right) -->
            <div class="flex justify-end space-x-4 mb-4">
                <button @click="toggleIndividualForm"
                    class="btn-primary text-white px-4 py-2 rounded-lg">
                    Add Individually
                </button>
                <button @click="toggleBulkForm" class="btn-primary text-white px-4 py-2 rounded-lg">
                    Add by Bulk
                </button>
                <button class="btn-primary text-white px-4 py-2 rounded-lg"
                    @click="downloadAllQRCodesAsPDF">Download All QR Codes as PDF</button>
            </div>

            <!-- Add New Student Form (Individual) -->
            <div v-if="showIndividualForm" class="mt-6">
                <form @submit.prevent="addStudent">
                    <div class="flex flex-col md:flex-row items-center md:space-x-4 space-y-4 md:space-y-0">
                        <input type="text" v-model.trim="formOne.full_name" placeholder="Enter Full Name"
                            class="border border-gray-300 px-4 py-2 rounded-lg flex-1" required />
                        <input type="text" v-model.trim="formOne.unique_id" placeholder="Enter Unique ID"
                            class="border border-gray-300 px-4 py-2 rounded-lg flex-1" required />
                        <button type="submit" class="btn-primary text-white px-4 py-2 rounded-lg">
                            Add Student
                        </button>
                    </div>
                </form>
            </div>

            <!-- Add Students by Bulk -->
            <div v-if="showBulkForm" class="mt-6">
                <form @submit.prevent="addStudentsBulk">
                    <textarea v-model="bulkInput" placeholder="Paste here one member per line
Format: Full Name,UniqueId(student id, etc.)
Example: Josh M. Ghad, 2022-7890
                    " class="border border-gray-300 w-full h-32 p-4 rounded-lg mb-4" required></textarea>
                    <input type="file" @change="handleFileUpload" class="mb-4" />
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                        Add Students by Bulk
                    </button>
                </form>
            </div>

            <!-- QR Code Pop-up -->
            <div v-if="showQRCodeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white p-6 rounded-lg shadow-lg relative w-full max-w-md flex flex-col">
                    <button @click="closeQRCode" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                        Close
                    </button>
                    <h3 class="text-xl font-bold mb-4 text-center">QR Code for {{ selectedMember }}</h3>
                    <div class="flex justify-center mb-4">
                        <qrcode-vue :value="selectedUniqueId" :size="200" :bgColor="'#ffffff'" :fgColor="'#000000'" />
                    </div>
                    <button @click="downloadQRCode"
                        class="bg-green-500 text-white px-4 py-2 rounded-lg mt-4 hover:bg-green-600">
                        Download QR Code
                    </button>
                </div>
            </div>

            <div class="hidden">
                <div v-for="member in master_list_members" :key="member.unique_id">
                    <p id="names">{{ member.full_name }}</p>
                    <qrcode-vue id="qrCode" :value="member.unique_id" :size="200" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import QrcodeVue from 'qrcode.vue';
import jsPDF from 'jspdf';
import QRCode from 'qrcode';
import * as XLSX from 'xlsx';

const props = defineProps({
    master_list: Object,
    master_list_members: Array,
});

const formOne = useForm({
    type: "individual",
    full_name: '',
    unique_id: '',
});

const formMany = useForm({
    type: "bulk",
    members: [],
});

const bulkInput = ref('');
const showIndividualForm = ref(false);
const showBulkForm = ref(false);
const showQRCodeModal = ref(false);
const selectedUniqueId = ref('');
const selectedMember = ref('');

// Toggle Individual Form
const toggleIndividualForm = () => {
    showIndividualForm.value = !showIndividualForm.value;
    showBulkForm.value = false;
};

// Toggle Bulk Form
const toggleBulkForm = () => {
    showBulkForm.value = !showBulkForm.value;
    showIndividualForm.value = false;
};

// Handle file upload and parse XLSX data (start reading from second row)
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const sheetName = workbook.SheetNames[0];
            const worksheet = workbook.Sheets[sheetName];
            const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            bulkInput.value = jsonData
                .slice(1) // Skip the first row (header row)
                .map(row => `${row[0]}, ${row[1]}`) // Use the first and second columns
                .join("\n");
        };
        reader.readAsArrayBuffer(file);
    }
};


// Watch the bulkInput and update formMany.members in real-time
watch(bulkInput, (newValue) => {
    const lines = newValue.split('\n');
    formMany.members = [];
    for (let i = 0; i < lines.length; i++) {
        const line = lines[i].split(',');
        if (line.length === 2 && line[0].trim() !== "" && line[1].trim() !== "") {
            const full_name = line[0].trim();
            const unique_id = line[1].trim();
            const exists = formMany.members.some(member => member.unique_id === unique_id);
            if (!exists) {
                formMany.members.push({ full_name, unique_id });
            }
        }
    }
});

// Method to add a member to the master list (individual)
const addStudent = () => {
    formOne.post(`/master-list-members/${props.master_list.master_list_id}`, {
        full_name: formOne.full_name,
        unique_id: formOne.unique_id,
        onSuccess: () => {
            formOne.reset(); // Reset form fields after successful submission
        }
    });
};

// Method to add members by bulk
const addStudentsBulk = () => {
    formMany.post(`/master-list-members/${props.master_list.master_list_id}`, {
        members: formMany.members,
        onSuccess: () => {
            bulkInput.value = ''; // Clear the bulk input field
            formMany.reset(); // Reset form fields after successful submission
        }
    });
};

// Show QR Code modal
const showQRCode = (uniqueId, fullName) => {
    selectedUniqueId.value = uniqueId;
    selectedMember.value = fullName;
    showQRCodeModal.value = true;
};

// Close QR Code modal
const closeQRCode = () => {
    showQRCodeModal.value = false;
};

// Download QR Code
const downloadQRCode = () => {
    const canvas = document.querySelector('canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `qrcode-${selectedUniqueId.value}.png`;
        link.click();
    }
};


const downloadAllQRCodesAsPDF = async () => {
    const pdf = new jsPDF();
    const members = props.master_list_members;
    const margin = 10;
    const qrCodeSize = 50; // Size of QR Code
    const qrCodeSpacing = 70; // Space between QR codes
    const pageHeight = pdf.internal.pageSize.height;
    let currentY = margin; // Track the current Y position

    for (let i = 0; i < members.length; i++) {
        const member = members[i];
        const imgData = await QRCode.toDataURL(member.unique_id, {
            width: 200,
            margin: 1,
        });

        // Check if we need to add a new page
        if (currentY + qrCodeSize + margin > pageHeight) {
            pdf.addPage();
            currentY = margin; // Reset Y position after adding a new page
        }

        // Center the QR code and text
        const centerX = (pdf.internal.pageSize.width - qrCodeSize) / 2;

        pdf.addImage(imgData, 'PNG', centerX, currentY, qrCodeSize, qrCodeSize);
        pdf.text(member.full_name, centerX, currentY + qrCodeSize + 5); // 5 pixels below the QR code

        // Move the Y position for the next QR code
        currentY += qrCodeSpacing;
    }

    pdf.save(`${props.master_list.name}-QRCodes.pdf`);
};

</script>
