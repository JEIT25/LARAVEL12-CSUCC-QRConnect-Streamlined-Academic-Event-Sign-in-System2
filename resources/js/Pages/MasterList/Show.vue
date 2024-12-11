<template>
    <div class="container mx-auto px-4 py-6">
        <div class="bg-gray-100 shadow-lg rounded-lg p-6">
            <!-- Master List Header -->
            <div class="flex flex-col md:flex-row items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">{{ props.master_list.name }}</h1>
                <!-- Delete Master List Button -->
                <Link :href="`/events/${props.master_list.event_id}/master-lists/${props.master_list.master_list_id}`"
                    as="button" method="delete" class="text-red-500 text-right hover:text-red-700 mt-2 md:mt-0">
                Delete Master List
                </Link>
            </div>

            <!-- Students Table -->
            <div class="overflow-x-auto mb-6">
                <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                    <thead>
                        <tr class="bg-gray-200 text-gray-700">
                            <th class="py-2 px-4 border-b">#</th> <!-- Column for row numbers -->
                            <th class="py-2 px-4 border-b">Name</th>
                            <th class="py-2 px-4 border-b">Unique ID</th>
                            <th class="py-2 px-4 border-b">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(member, index) in props.master_list_members" :key="member.master_list_member_id">
                            <td class="py-2 px-4 border-b text-center">{{ index + 1 }}</td> <!-- Display row number -->
                            <td class="py-2 px-4 border-b text-center">{{ member.full_name }}</td>
                            <td class="py-2 px-4 border-b text-center">{{ member.unique_id }}</td>
                            <td class="py-2 px-4 border-b text-center">
                                <Link class="bg-red-500 text-white hover:bg-red-600 px-2 py-1 rounded"
                                    :href="`/master-list-members/${member.master_list_member_id}`" as="button"
                                    method="delete">
                                Delete
                                </Link>
                                <button @click="showQRCode(member.unique_id, member.full_name)"
                                    class="btn-primary text-sm md:text-base text-white px-2 py-1 rounded mt-1 md:mt-0 md:ml-2">
                                    QR Code
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Add Student Buttons (aligned to the right) -->
            <div class="flex flex-col md:flex-row justify-end space-y-2 md:space-y-0 md:space-x-4 mb-4">
                <button @click="toggleIndividualForm" class="btn-primary text-white px-4 py-2 rounded-lg">
                    Add Individually
                </button>
                <button @click="toggleBulkForm" class="btn-primary text-white px-4 py-2 rounded-lg">
                    Add by Bulk
                </button>
                <button class="btn-primary text-white px-4 py-2 rounded-lg" @click="downloadAllQRCodesAsPDF">Download
                    All QR Codes as PDF</button>
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
            <div v-if="showBulkForm" class="mt-6 p-4 bg-white rounded-lg shadow-md">
                <form @submit.prevent="addStudentsBulk" class="space-y-4">
                    <textarea v-model.trim="bulkInput" placeholder="Enter here one member per line
Format: UniqueId(student id, etc.), Full Name
Example: , 2022-7890, Josh M. Ghad"
                        class="border border-gray-300 w-full h-32 p-4 rounded-lg resize-none focus:outline-none focus:ring-2 focus:ring-slate-500"
                        required></textarea>
                    <input type="file" @change="handleFileUpload"
                        class="border border-gray-300 w-full p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 mb-4" />
                    <select v-model="selectedSheet" @change="handleSheetChange"
                        class="border border-gray-300 w-full p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 mb-4">
                        <option value="" disable>Select Sheet</option>
                        <option v-for="sheet in sheets" :key="sheet" :value="sheet">{{ sheet }}</option>
                    </select>
                    <button type="submit" class="w-full btn-primary">
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
const sheets = ref([]); // To store sheet names
const selectedSheet = ref(''); // To store the selected sheet

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

// Handle file upload and parse XLSX data
const handleFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });

            // Get the sheet names and populate the sheets array
            sheets.value = workbook.SheetNames;

            // Automatically set the first sheet as the selected sheet
            if (sheets.value.length > 0) {
                selectedSheet.value = sheets.value[0];
                handleSheetChange(); // Load the first sheet data
            }
        };
        reader.readAsArrayBuffer(file);
    }
};

// Handle sheet change and read data from the selected sheet
const handleSheetChange = () => {
    const file = document.querySelector('input[type="file"]').files[0];
    if (file && selectedSheet.value) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: "array" });
            const worksheet = workbook.Sheets[selectedSheet.value];
            const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

            bulkInput.value = jsonData
                .slice(1) // Skip the first row (header row)
                .filter(row => row && row.length > 0) // Ensure the row is not empty or invalid
                .map(row => {
                    const id = row[0] ? row[0].trim() : ""; // Handle ID Number
                    const firstName = row[2] ? row[2].trim() : ""; // Handle FirstName
                    const lastName = row[1] ? row[1].trim() : ""; // Handle LastName

                    // Only concatenate if values exist
                    const fullName = `${firstName} ${lastName}`.trim();
                    return `${id}, ${fullName}`;
                })
                .join('\n'); // Join entries into a single string with line breaks
        };
        reader.readAsArrayBuffer(file);
    }
};

// Add Student (Individual)
const addStudent = () => {
    formOne.post(`/master-list-members/${props.master_list.master_list_id}`, {
        onSuccess: () => {
            formOne.reset();
        },
    });
};

// Add Students (Bulk)
const addStudentsBulk = () => {
    const members = bulkInput.value.split('\n').map(line => {
        const [uniqueId, fullName] = line.split(',').map(item => item.trim());
        return { unique_id: uniqueId, full_name: fullName };
    });

    formMany.members = members;

    formMany.post(`/master-list-members/${props.master_list.master_list_id}`, {
        onSuccess: () => {
            bulkInput.value = ''; // Clear the bulk input after submission
            showBulkForm.value = false; // Close bulk form
        },
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

// Function to generate QR code data URL
const generateQRCode = async (uniqueId) => {
    try {
        // Generate QR code as base64 data URL
        return await QRCode.toDataURL(uniqueId);  // Returns base64 string
    } catch (error) {
        console.error('Error generating QR code:', error);
    }
};

// Download QR Code as PDF
const downloadQRCode = async () => {
    const uniqueId = selectedUniqueId.value;  // Unique ID of the member
    const memberName = selectedMember.value;  // Name of the selected member

    // Generate the QR code image as base64 string
    const qrCodeDataUrl = await generateQRCode(uniqueId);

    // Create a PDF document with A4 size (smaller size than default)
    const pdf = new jsPDF({ unit: 'mm', format: 'a4' });

    // Set the font size to a larger value for the text
    pdf.setFontSize(20);  // Increased font size to 20 (or any value you prefer)

    // Calculate the width of the text and center it horizontally
    const textWidth = pdf.getTextWidth(`QR Code for: ${memberName}`);
    const pageWidth = pdf.internal.pageSize.width;
    const xPositionText = (pageWidth - textWidth) / 2;

    // Add text (centered) with larger font size
    pdf.text(`QR Code for: ${memberName}`, xPositionText, 20);

    // Increase the size of the QR code image (make it larger)
    const qrWidth = 100; // Increase width of the image
    const qrHeight = 100; // Increase height of the image

    // Calculate the horizontal position for the QR code (centered)
    const xPositionQR = (pageWidth - qrWidth) / 2;

    // Add the QR code image to the PDF (centered horizontally)
    pdf.addImage(qrCodeDataUrl, 'PNG', xPositionQR, 30, qrWidth, qrHeight);

    // Save the PDF with the name of the member
    pdf.save(`${memberName}-QRCode.pdf`);
};


// Download all QR Codes as PDF
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

    pdf.save(`${props.master_list.name} - QRCodes.pdf`);
};
</script>
